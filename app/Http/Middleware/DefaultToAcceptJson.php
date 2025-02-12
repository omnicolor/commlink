<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DefaultToAcceptJson
{
    public function handle(Request $request, Closure $next): JsonResponse | Response
    {
        // If the request does not have an Accept header, set it to
        // application/json.
        if (null === $request->header('Accept')) {
            $request->headers->set('Accept', 'application/json');
        }
        return $next($request);
    }
}
