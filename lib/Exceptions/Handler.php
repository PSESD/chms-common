<?php
/**
 * Clock Hour Management System - Portal API
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use CHMS\Common\Contracts\ErrorMessageBagException;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Database\Eloquent\MassAssignmentException;
use \Symfony\Component\HttpKernel\Exception\GoneHttpException;
use \Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use \Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use \Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use \Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

use \League\OAuth2\Server\Exception\OAuthException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        ExpiredException::class,
        GoneHttpException::class,
        ValidationException::class,
        ConflictHttpException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        BadRequestHttpException::class,
        UnexpectedValueException::class,
        AccessDeniedHttpException::class,
        SignatureInvalidException::class,
        UnauthorizedHttpException::class,
        NotAcceptableHttpException::class,
        LengthRequiredHttpException::class,
        TooManyRequestsHttpException::class,
        MethodNotAllowedHttpException::class,
        PreconditionFailedHttpException::class,
        PreconditionRequiredHttpException::class,
        UnsupportedMediaTypeHttpException::class,
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
        parent::report($e);
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
        $packageResponse = [];
        $statusCode = 500;
        if (($message = $e->getMessage())) {
            $packageResponse['title'] = $message;
        }
        if ($e instanceof OAuthException) {
            $e = new AccessDeniedHttpException($e->getMessage());
        }
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }
        if ($e instanceof ErrorMessageBagException && $e->hasErrors()) {
            $packageResponse['errors'] = $e->getErrors();
        }

        return response()->json($packageResponse, $statusCode);
    }
}
