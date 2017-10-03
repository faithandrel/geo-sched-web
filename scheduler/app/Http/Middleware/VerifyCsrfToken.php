<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
       'test-save-from-app',
       'fb-sign-up-from-app',
       'fb-sign-up',
       'password-log-in',
       'facebook-log-in',
       'save-item',
       'emoji',
    ];
}
