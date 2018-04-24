<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'last_active',
        'last_notified',
    ];

    
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
