<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

use function count;
use function redirect;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * @codeCoverageIgnore
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$guards,
    ): RedirectResponse | Redirector | Response {
        $guards = 0 === count($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
