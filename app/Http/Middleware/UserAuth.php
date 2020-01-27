<?php

namespace App\Http\Middleware;

use Closure;

class UserAuth
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
        echo var_dump($request->all()['user']);
        // echo dd($request->has('user'));
        echo var_dump($request->only(['user','passwd']));
        return $next($request);
    }
}
