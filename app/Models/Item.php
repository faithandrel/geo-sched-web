<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title', 
        'content',
        'user_id',
        'item_id',
    ];

    protected $hidden = [
        'pivot', 
    ];
    
    public function getContentAttribute($content) {
        return utf8_decode($content);
    }

    public function getTitleAttribute($title) {
        return utf8_decode($title);
    }

    public function locations() {
        return $this->morphMany(Location::class, 'locationable');
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function comments() {
        return $this->hasMany(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
