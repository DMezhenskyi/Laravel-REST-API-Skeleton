<?php

namespace App\Exceptions;

use App\Http\Controllers\API\v1\BaseApiController as ApiController;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\ModelsExceptions\DBException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json(ApiController::prepareResponse(true, []), 404);
        }

        if ($e instanceof DBException) {
            return response()->json(ApiController::prepareResponse(false, [$e->getMessage()]), 500);
        }

        if ($e instanceof TokenExpiredException) {
            return response()->json(ApiController::prepareResponse(false, [$e->getMessage()]), $e->getStatusCode());
        }

        if ($e instanceof TokenInvalidException) {
            return response()->json(ApiController::prepareResponse(false, [$e->getMessage()]), $e->getStatusCode());
        }

        if ($e instanceof JWTException) {
            return response()->json(ApiController::prepareResponse(false, [$e->getMessage()]), $e->getStatusCode());
        }

        return parent::render($request, $e);
    }
}
