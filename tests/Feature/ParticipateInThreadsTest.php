<?php

namespace Tests\Feature;


use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()->post('/threads/some-channel/1/replies', [])->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->be($user = User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies',['body' => $reply->body]);

        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make(['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = Reply::factory()->create();

        $this->delete("/replies/{$reply->id}")->assertRedirect('/login');

        $this->actingAs(User::factory()->create());

        $this->delete("/replies/{$reply->id}")->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->actingAs(User::factory()->create());

        $reply = Reply::factory()->create(['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0,$reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $reply = Reply::factory()->create();

        $this->patch("/replies/{$reply->id}")->assertRedirect('login');

        $this->actingAs(User::factory()->create())->patch("/replies/{$reply->id}")->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_replies()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $reply = Reply::factory()->create(['user_id' => auth()->id()]);

        $this->patch("/replies/{$reply->id}", ['body' => 'Changed']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'Changed']);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
//        $this->withoutExceptionHandling();

        $this->be($user = User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make(['body' => 'Yahoo Customer Support']);

        $this->expectException(\Exception::class);

        $this->post($thread->path() . '/replies', $reply->toArray());
    }
}
