<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->post('/threads')->assertRedirect('login');

        $this->get('/threads/create')->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->make();

        $response = $this->post('/threads', $thread->toArray());

        $this->get($response->headers->get('Location'))->assertSee($thread->title)->assertSee($thread->body);
    }

    /** @test  */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test  */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }
    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])->assertSessionHasErrors('channel_id');
    }

    public function publishThread($overrides = [])
    {
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->make($overrides);

        return $this->post('/threads',$thread->toArray());
    }
}
