<?php

namespace App\Http\Middleware;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Session\TokenMismatchException;

use Closure;
use Hash;
use Config;
use Log;

//deprecated
class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    protected $encrypter;
    
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }
    
    public function handle($request, Closure $next)
    {
        //Log::info('App Request: '.json_encode($request->header()));
        if ( $this->isReadingFromApp($request) ) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Origin, Accept');
        }
        else if($this->appTokensMatch($request)) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Origin, Accept');
        }

        throw new TokenMismatchException;
    
    }
    
    protected function appTokensMatch($request)
    {
        if (Hash::check(Config::get('app.mobile_app_token'),
                        $request->input('_token'))) {
            return true;
        }
        else {
            return false;
        }
    }
    
    protected function isReadingFromApp($request)
    {
        return in_array($request->method(), ['GET', 'OPTIONS']);
    }
}
