<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\ResponsableFonctionPubliqueMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->reportable(function (\Throwable $e): void {
        \Illuminate\Support\Facades\Log::channel('stderr')
            ->error('PNAE-RCA : exception Laravel', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
    });
})
    ->create();