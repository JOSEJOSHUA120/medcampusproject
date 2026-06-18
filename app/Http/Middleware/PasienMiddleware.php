<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PasienMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'pasien') {
            abort(403);
        }
        return $next($request);
    }
}
