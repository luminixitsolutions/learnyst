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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            return match ($user?->role?->slug) {
                'super-admin' => route('platform.dashboard'),
                'admin', 'sub-admin' => route('admin.dashboard'),
                'instructor' => route('instructor.dashboard'),
                'learner' => route('learner.dashboard'),
                default => route('home'),
            };
        });
        $middleware->redirectGuestsTo(function ($request) {
            if (
                $request->is('learner', 'learner/*')
                || $request->is('student/*')
                || $request->is('courses/*/checkout')
                || $request->is('courses/checkout/*')
            ) {
                return route('student.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
