<?php

namespace App\Http\Middleware;

// Design Pattern: Chain of Responsibility — Middleware diproses berantai sebelum request mencapai Controller
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * OOP Concept: Single Responsibility — Middleware hanya bertugas memeriksa role user
     * Jika user bukan admin, hentikan request dengan 403 Forbidden
     * Jika valid, lanjutkan ke middleware/controller berikutnya via $next($request)
     */
    public function handle(Request $request, Closure $next)
    {
        // Encapsulation: akses user melalui method request()->user()
        if (!$request->user() || $request->user()->role !== 'admin') {
            abort(403);
        }
        return $next($request);
    }
}
