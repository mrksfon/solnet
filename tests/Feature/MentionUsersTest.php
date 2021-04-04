<?php

namespace Tests\Feature;


use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $john = User::factory()->create(['name' => 'JohnDoe']);

        $this->actingAs($john);

        $jane = User::factory()->create(['name' => 'JaneDoe']);

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make(['body' => '@JaneDoe look at this also @FrankDoe']);

        $this->json('POST', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }
}
