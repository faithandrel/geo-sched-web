<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title', 
        'content',
        'user_id',
    ];
    
    public function getContentAttribute($content) {
        return utf8_decode($content);
    }

    public function locations() {
        return $this->morphMany(Location::class, 'locationable');
    }
}
