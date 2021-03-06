<?php namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class AdminDebugBar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            \Debugbar::enable();
        } else {
            \Debugbar::disable();
        }

        return $next($request);
    }
}