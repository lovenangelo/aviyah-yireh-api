<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;

class SkipCsrfToken extends VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If X-Request-Token header is present, skip CSRF token verification
        if ($request->hasHeader('X-Request-Token')) {
            return $next($request);
        }

        // For regular requests, continue with parent CSRF protection
        return parent::handle($request, $next);
    }
}
