<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class UpdateLastActive
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
        $user = auth()->user();

        $user->last_active = Carbon::now();
        $user->save();
         
        return $next($request);
    }
}
