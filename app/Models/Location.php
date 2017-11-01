<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'latitude', 
        'longitude',
        'locationable_id',
        'locationable_type',
    ];

    public function locationable() {
		return $this->morphTo();
	}
}
