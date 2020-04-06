<?php

namespace App\Http\Middleware;

use Closure;

class filterTime
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
        // 过滤掉时间
        $request->offsetUnset('created_at');
        $request->offsetUnset('updated_at');
        return $next($request);
    }
}
