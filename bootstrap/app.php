<?php

use App\Http\Middleware\MinifyHtml;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrackVisitor;
use App\Notifications\SystemMonitorNotification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SecurityHeaders::class,
            TrackVisitor::class,
            MinifyHtml::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            // Jangan kirim notifikasi jika di lingkungan lokal, testing, atau mode debug aktif
            if (app()->environment('local', 'testing') || app()->runningUnitTests() || config('app.debug')) {
                return;
            }

            // Jangan kirim notifikasi untuk error biasa (404, validasi, auth)
            if ($e instanceof NotFoundHttpException ||
                $e instanceof ValidationException ||
                $e instanceof AuthenticationException) {
                return;
            }

            try {
                $method = request()->method();
                $url = request()->fullUrl();
                $msg = class_basename($e)."\n"
                        .'📄 '.basename($e->getFile()).":{$e->getLine()}\n"
                        .'💬 <code>'.substr($e->getMessage(), 0, 300)."</code>\n"
                        ."\n🔗 {$method} {$url}";

                Notification::route('telegram', 'system')
                    ->notify(new SystemMonitorNotification('SYSTEM ERROR', $msg, 'danger'));
            } catch (Throwable $err) {
                // Abaikan jika Telegram juga error agar tidak terjadi infinite loop
            }
        });
    })->create();
