<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function a_user_can_browse_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = Channel::factory()->create();

        $threadInChannel = Thread::factory()->create(['channel_id' => $channel->id]);
        $threadNotInChannel = Thread::factory()->create(['channel_id' => 999]);

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $user = User::factory()->create(['name' => 'John']);

        $this->actingAs($user);

        $threadByJohn = Thread::factory()->create(['user_id' => $user->id]);

        $threadNotByJohn = Thread::factory()->create();

        $this->get('threads?by=John')->assertSee($threadByJohn->title)->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = Thread::factory()->create();
        Reply::factory()->count(2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = Thread::factory()->create();
        Reply::factory()->count(3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $threadWithZeroReplies = $this->thread;

        $response = $this->getJson('/threads?popular=1')->json();

        $this->assertEquals([3,2,0],array_column($response,'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = Thread::factory()->create();

        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $response = $this->getJson('/threads?unanswered=1')->json();

        $this->assertCount(1,$response);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $this->withoutExceptionHandling();

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->count(2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2,$response['data']);

        $this->assertEquals(2,$response['total']);
    }
}
