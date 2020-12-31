<?php
namespace Citsk\Controllers;

use Citsk\Exceptions\DataBaseException;
use Citsk\Library\Shared;
use Exception;

class Controller
{

    /**
     * @return object|null
     */
    public function callRequestedMethod(): ?array
    {

        global $ROUTE;

        $action = $ROUTE['action'];

        //controller methods
        if (method_exists($this, $action)) {

            try {
                return call_user_func([$this, $action]);
            } catch (DataBaseException | Exception $e) {

                DB_DEBUG
                ? $this->errorResponse($e->getCode(), $e->getMessage())
                : $this->errorResponse();

            }
        }

        //model methods
        if (method_exists($this->model, $action)) {

            try {
                $data = call_user_func([$this->model, $action]);
                $this->dataResponse($data);
            } catch (DataBaseException | Exception $e) {

                DB_DEBUG
                ? $this->errorResponse($e->getCode(), $e->getMessage())
                : $this->errorResponse();
            }
        }

        die(http_response_code(404));
    }

    /**
     * @param array|null $inputData
     *
     * @return array
     */
    protected function getBindingParams(?array $inputData = null): array
    {

        $inputData = $inputData ?? $_POST;
        $params    = [];

        array_walk($inputData, function ($value, $key) use (&$params) {
            $params[Shared::toSnakeCase($key)] = $value;
        });

        return $params;
    }

    /**
     * @param string $HTTPMethod
     *
     * @return void
     */
    protected function setHTTPMethod($HTTPMethod = "POST"): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($HTTPMethod)) {
            die(http_response_code(405));
        }
    }

    /**
     * @return void
     */
    protected function isUserAuthorized(): void
    {

        if (!$this->user->isAuthorized) {
            die(http_response_code(403));
        }
    }

    /**
     * @return void
     */
    protected function checkAdminAccess(): void
    {
        if (!$this->user->isAdmin) {
            die(http_response_code(403));
        }
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    protected function dataResponse($data): void
    {
        $result = [];

        if (is_array($data)) {
            $result = $data;
        }

        if (is_object($data)) {

            if ($data->structure) {
                $result = count($data->structure) > 1
                ? $data->structure
                : $data->structure[0];
            }
        }

        die(json_encode($result));
    }

    /**
     * @param array|null $data
     * @param int $status
     *
     * @return void
     */
    protected function successResponse(?array $data = null, int $status = 1): void
    {
        $response = [
            "status" => $status,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        die(json_encode($response));
    }

    /**
     * @param int $status
     * @param array|null $data
     *
     * @return void
     */
    protected function errorResponse(int $status = 0, ?string $errorMessage = null): void
    {
        $response = [
            "status" => $status,
        ];

        if ($errorMessage) {
            $response['error'] = $errorMessage;
        }

        die(json_encode($response));
    }

}
