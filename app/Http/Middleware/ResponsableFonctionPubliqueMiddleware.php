<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponsableFonctionPubliqueMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless(
            $user !== null
            && $user->isResponsableFonctionPublique(),
            403,
            'Cette opération est réservée au responsable du ministère de la Fonction publique.'
        );

        return $next($request);
    }
}