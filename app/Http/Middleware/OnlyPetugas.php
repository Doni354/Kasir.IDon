<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyPetugas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return redirect('/home'); // Redirect jika bukan petugas
        }
        return $next($request); // Lanjutkan request jika role adalah petugas
    }
}
