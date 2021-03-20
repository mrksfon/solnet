<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create();

        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1,$thread->fresh()->subscriptions);
    }

    /** @test */
    public function a_user_can_unsubscribe_from_a_thread()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create();

        $thread->subscribe();

        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0,$thread->subscriptions);
    }
}
