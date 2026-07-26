<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\TrackVisitor::class,
            \App\Http\Middleware\MinifyHtml::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            // Jangan kirim notifikasi untuk error biasa (404, validasi, auth)
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\AuthenticationException) {
                return;
            }

            try {
                $msg = "🔥 <b>Error:</b> " . class_basename($e) . "\n";
                $msg .= "📄 <b>File:</b> <code>" . basename($e->getFile()) . ":" . $e->getLine() . "</code>\n";
                $msg .= "💬 <b>Pesan:</b> <pre>" . substr($e->getMessage(), 0, 500) . "</pre>\n";
                $msg .= "🔗 <b>URL:</b> " . request()->fullUrl();

                \Illuminate\Support\Facades\Notification::route('telegram', 'system')
                    ->notify(new \App\Notifications\SystemMonitorNotification('SYSTEM ERROR (500)', $msg, 'danger'));
            } catch (\Throwable $err) {
                // Abaikan jika Telegram juga error agar tidak terjadi infinite loop
            }
        });
    })->create();
