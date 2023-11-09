<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->view('error.auth-404-basic', [], 404);
        }

        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            switch ($statusCode) {
                case 500:
                    return response()->view('error.auth-500', [], 500);
                    break;
                default:
                    return response()->view('error.general', ['exception' => $e], $statusCode);
            }
        }

        return parent::render($request, $e);
    }



}
