<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd(session()->all());
        if (session()->missing('FILTER_APP')) {
            return redirect('/auth/login#4');
        }
        // dd(session('auth_token'));
        if (empty(session('auth_token'))) {
            return redirect()->intended('/auth/login#1');
        }
        if (session('auth_token') == '0') {
            return redirect()->intended('/auth/login#2');
        }

        $user = User::where('auth_login', session('auth_token'))->first();

        if (!$user) {
            return redirect()->intended('/auth/login/#3');
        }

        if (!$request->session()->has('auth_token')) {
            return redirect('/auth/login#4');
        }
        return $next($request);
    }
}
