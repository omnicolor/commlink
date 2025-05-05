<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Events\ApiKeyRevoked;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpFoundation\RequestMatcher\SchemeRequestMatcher;

use function abort;

class ExpireApiTokenSentUnencrypted
{
    public function handle(
        Request $request,
        Closure $next,
    ): JsonResponse | RedirectResponse | Redirector | Response | null {
        $schemeMatcher = new SchemeRequestMatcher('https');
        if (!$schemeMatcher->matches($request)) {
            $token = $request->user()?->currentAccessToken();
            if (null === $token || $token instanceof TransientToken) {
                return $next($request);
            }
            ApiKeyRevoked::dispatch($token);
            $token->delete();
            abort(
                Response::HTTP_FORBIDDEN,
                'Your API key has been revoked. Do not use API keys on an '
                    . 'unsecured connection.',
            );
        }
        return $next($request);
    }
}
