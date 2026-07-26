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
                $method = request()->method();
                $url    = request()->fullUrl();
                $msg    = class_basename($e) . "\n"
                        . "📄 " . basename($e->getFile()) . ":{$e->getLine()}\n"
                        . "💬 <code>" . substr($e->getMessage(), 0, 300) . "</code>\n"
                        . "\n🔗 {$method} {$url}";

                \Illuminate\Support\Facades\Notification::route('telegram', 'system')
                    ->notify(new \App\Notifications\SystemMonitorNotification('SYSTEM ERROR', $msg, 'danger'));
            } catch (\Throwable $err) {
                // Abaikan jika Telegram juga error agar tidak terjadi infinite loop
            }
        });
    })->create();
