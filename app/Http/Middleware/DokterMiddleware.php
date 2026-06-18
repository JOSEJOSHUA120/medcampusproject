<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DokterMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'dokter') {
            abort(403);
        }
        return $next($request);
    }
}
