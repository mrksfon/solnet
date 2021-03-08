<?php

namespace App\Models;


use App\Favoritable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable;
    use HasFactory;

    protected $guarded = [];

    protected $with = ['owner','favorites'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
