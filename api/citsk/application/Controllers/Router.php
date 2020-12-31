<?php

namespace Citsk\Controllers;

use Citsk\Exceptions\RouterException;
use Citsk\Interfaces\Controllerable;
use Citsk\Library\Identity;
use Citsk\Library\Shared;
use Exception;

final class Router
{

    /**
     * @var string
     */
    private $requestedAction;

    /**
     * @var string
     */
    private $controllerNamespace = "Citsk\Controllers\\";

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @return Router
     */
    public function initializeParameters(): Router
    {

        $params = explode('/', $_SERVER['REQUEST_URI']);

        if (count($params) <= 2) {
            throw new RouterException("Handler is not defined in HTTP params");
        }

        $this->controllerName = $params[1];
        preg_match("/^([^?]+)(\?.*?)?(#.*)?$/", $params[2], $matches);
        $this->requestedAction = $matches[1];

        return $this;
    }

    /**
     * @return Router
     */
    public function setHTTPHeaders(): Router
    {
        if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') {
            die(http_response_code(404));
        }

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            die();
        }

        return $this;
    }

    /**
     * @return object
     */
    public function initializeRouting(): void
    {
        try {
            $this->initializeParameters();
            $this->getControllerName();

            if (!$this->isExistsController()) {
                $this->getControllerFromRouteCollection();
            }

            $this->initializeController();

        } catch (Exception $e) {
            if (DB_DEBUG) {

                $params = [
                    "error"  => $e->getMessage(),
                    "status" => $e->getCode(),
                ];

                die(json_encode($params));
            }

            die(http_response_code(404));
        }

    }

    /**
     * @return void
     */
    private function getControllerName(): void
    {
        if ($this->controllerName[strlen($this->controllerName) - 1] == "s") {
            $this->controllerName = substr($this->controllerName, 0, -1);
        }

        $this->controllerName = $this->controllerNamespace . ucfirst($this->controllerName) . "Controller";
    }

    /**
     * @return bool
     */
    private function isExistsController(?string $controllerName = null): bool
    {
        return class_exists($controllerName ?? $this->controllerName) ? true : false;

    }

    /**
     * @return void
     */
    private function getControllerFromRouteCollection(): void
    {

        if (file_exists(ROUTES)) {
            include_once ROUTES;

        } else {
            throw new RouterException("Route file not found");
        }

        $searchRouteMatch = function ($route) {

            if (preg_match($route['path'], $_SERVER['REQUEST_URI'])) {

                if ($this->isExistsController($route['controller'])) {
                    $this->controllerName = $route['controller'];
                } else {
                    throw new RouterException("Controller name not found in class loader", 201);
                }
            }
        };

        array_walk($routes, $searchRouteMatch);
    }

    /**
     * @return void
     */
    private function initializeController(): void
    {
        $controller = new $this->controllerName;

        if ($controller instanceof Controllerable) {
            global $ROUTE, $USER;

            $ROUTE['action']        = Shared::toCamelCase($this->requestedAction);
            $ROUTE['original_path'] = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            $USER                   = new Identity;

            if (empty($_POST)) {
                $_POST = json_decode(file_get_contents('php://input'), true);
            }

            $controller->initializeController();
            $controller->callRequestedMethod();

        } else {
            throw new RouterException("{$this->controllerName} is not the controller");
        }
    }
}
