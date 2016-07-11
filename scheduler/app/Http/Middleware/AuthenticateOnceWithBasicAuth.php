<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Log;

class AuthenticateOnceWithBasicAuth
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
        //Log::info('App Request Basic: '.json_encode($request->header()));
        return Auth::onceBasic() ?: $next($request);
    }
}
