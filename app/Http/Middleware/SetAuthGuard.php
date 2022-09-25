<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAuthGuard
{
    public function __construct(protected Auth $auth)
    {
    }

    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        $this->auth->shouldUse($guard);

        return $next($request);
    }
}
