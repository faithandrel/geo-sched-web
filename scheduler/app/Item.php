<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title', 'latitude', 'longitude',
    ];
    
     public function getContentAttribute($content) {
        return utf8_decode ($content);
     }
}
