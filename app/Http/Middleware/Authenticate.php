<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function __construct(protected Auth $auth)
    {
    }

    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        if ($this->auth->guard($guard)->guest()) {
            return match ($guard) {
                'session' => redirect('/session/login'),
                default => response('Unauthorized.', 401),
            };
        }

        return $next($request);
    }
}
