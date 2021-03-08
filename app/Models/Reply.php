<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite($userId)
    {
        $attributes = ['user_id' => $userId];
        if(!$this->favorites()->where($attributes)->exists()){
            return $this->favorites()->create($attributes);
        }
    }

    public function isFavorited()
    {
        return $this->favorites()->where('user_id',auth()->id())->exists();
    }
}
