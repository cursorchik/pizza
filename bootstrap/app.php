<?php

use App\Http\Middleware as CustomMiddleware;

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) : void
    {
        $middleware->alias([
            'admin' => CustomMiddleware\CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions)
    {
        // Обработка 404
        $exceptions->render(function (NotFoundHttpException $e, Request $request)
        {
            if ($request->is('api/*') || $request->wantsJson())
            {
                if (str_contains($e->getMessage(), 'No query results for model'))
                {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Resource not found',
                    ], 404);
                }

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Not found',
                ], 404);
            }
        });

        // Обработка 403 (политики авторизации)
        $exceptions->render(function (AuthorizationException $e, Request $request)
        {
            if ($request->is('api/*') || $request->wantsJson())
            {
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage() ?: 'Forbidden',
                ], 403);
            }
        });
    })->create();
