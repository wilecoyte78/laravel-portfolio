<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Mail\ExceptionOccurredMail;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Port 8000 is bound to the VPS's own loopback only (see
        // docker-compose.yml), so Apache is the only realistic caller here.
        // We trust all proxies rather than a specific IP because Docker's
        // port-forwarding rewrites the source address before it reaches
        // Swoole - it won't literally be 127.0.0.1 from inside the container.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            $recipient = config('mail.exception_recipient');

            if (! app()->environment('production') && ! $recipient) {
                return;
            }

            // Dedupe: one email per unique exception per 10 minutes
            $key = 'exception-mail:'.md5($e::class.$e->getFile().$e->getLine());

            if (! Cache::add($key, true, now()->addMinutes(10))) {
                return;
            }

            try {
                $request = request();

                Mail::to($recipient)
                    ->queue(new ExceptionOccurredMail([
                        'class' => $e::class,
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => Str::limit($e->getTraceAsString(), 5000),
                        'url' => app()->runningInConsole() ? 'CLI: '.implode(' ', $_SERVER['argv'] ?? []) : $request->fullUrl(),
                        'method' => app()->runningInConsole() ? null : $request->method(),
                        'ip' => app()->runningInConsole() ? null : $request->ip(),
                        'user' => optional($request->user())->only(['id', 'email']),
                        'env' => app()->environment(),
                        'time' => now()->toDateTimeString(),
                    ]));
            } catch (Throwable) {
                // Never let error reporting throw its own error
            }
        });
    })->create();
