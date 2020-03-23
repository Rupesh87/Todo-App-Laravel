<?php

namespace App\Http\Middleware;

use Closure;

class Staff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->auth = auth()->user() ? (auth()->user()->is_admin === 0) : false;
        if($this->auth === true)
            return $next($request);
        return redirect()->route('user.index')->with('error', 'Access denied. Please login as a staff.');
    }
}
