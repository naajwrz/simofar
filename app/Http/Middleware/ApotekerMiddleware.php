<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApotekerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isApoteker()) {
            return redirect('/login')->withErrors(['email' => 'Akses khusus untuk Apoteker.']);
        }

        return $next($request);
    }
}