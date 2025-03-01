<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Omnicolor\Slack\Exceptions\SlackException;
use Override;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        SlackException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    #[Override]
    public function register(): void
    {
        parent::register();
        $this->reportable(function (Throwable $e): void {
            Integration::captureUnhandledException($e);
        });
    }
}
