<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
//        if ($exception instanceof QueryException) {
//            return response('Server Error', 500);
//        }

        if ($exception instanceof \Swift_TransportException) {
            return response('Internet Connection Failed', 503);
        }

        if ($exception instanceof OAuthServerException) {
            return response()->json([
                'error' => $exception->getErrorType(),
                'message' => $exception->getMessage(),
                'hint' => $exception->getHint(),
                'http_status' => $exception->getHttpStatusCode(),
                'code' => $exception->getCode()
            ], $exception->getHttpStatusCode());
        }

        return parent::render($request, $exception);
    }
}
