<?php

namespace App\Exceptions;

use App\Lib\Constant;
use App\Traits\Response as TraitResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use PDOException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use TraitResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * @param mixed $request
     * @param Throwable $e
     *
     * @return mixed
     */
    public function render($request, Throwable $e): mixed
    {

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return TraitResponse::jsonError(404, Constant::NOT_FOUND_MESSAGE, Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof AuthenticationException) {
            return TraitResponse::jsonUnathorized();
        }

        if ($e instanceof ForbiddenException || $e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return TraitResponse::jsonForbidden($e->getMessage());
        }

        if ($e instanceof PDOException) {
            $message = $e->getMessage();

            if (is_numeric(stripos($message, "Duplicate"))) {
                return TraitResponse::jsonError(1062, env('APP_DEBUG') ? $e->getMessage() : preg_replace("/1062(?s)(.*) \(SQL/", "", $e->getMessage()));
            }

            if (is_numeric(stripos($message, "Integrity constraint violation"))) {
                return TraitResponse::jsonError(1451, env('APP_DEBUG') ? $e->getMessage() : preg_replace("/1451(?s)(.*) \(SQL/", "", $e->getMessage()));
            }
        }

        return parent::render($request, $e);
    }
}
