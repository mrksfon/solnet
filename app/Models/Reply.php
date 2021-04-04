<?php

namespace App\Models;


use App\Favoritable;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reply
 *
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Favorite[] $favorites
 * @property-read int|null $favorites_count
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|Reply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reply query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reply whereUserId($value)
 * @mixin \Eloquent
 */
class Reply extends Model
{
    use Favoritable;
    use HasFactory;
    use RecordsActivity;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleting(function ($reply) {
            $reply->favorites->each->delete();
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    protected $guarded = [];

    protected $with = ['owner', 'favorites'];

    protected $appends = ['favoritesCount', 'isFavorited'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(now()->subMinute());
    }

    public function mentionedUsers()
    {
        preg_match_all('/\@([^\s\.]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }
}
