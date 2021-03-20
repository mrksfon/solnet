<?php

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();
        $this->thread =Thread::factory()->create();
    }

    /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $thread = Thread::factory()->create();

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}",$thread->path());
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$this->thread->replies);
    }

    /** @test */
    public function a_thread_has_a_creator()
    {
        $this->assertInstanceOf(User::class,$this->thread->creator);
    }

    /** @test */
    public function a_thread_can_add_reply()
    {
        $this->thread->addReply([
           'body' => 'Foobar',
           'user_id' => 1
        ]);

        $this->assertCount(1,$this->thread->replies);
    }

    /** @test */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->actingAs(User::factory()->create());

        $this->actingAs(User::factory()->create())->thread->subscribe()->addReply([
            'body' => 'Foobar',
            'user_id' => 999
        ]);

        Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel ()
    {
        $thread = Thread::factory()->create();

        $this->assertInstanceOf(Channel::class,$thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        $thread = Thread::factory()->create();

        $thread->subscribe($userId = 1);

        $this->assertEquals(1,$thread->subscriptions()->where('user_id',$userId)->count());
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $thread = Thread::factory()->create();

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0,$thread->subscriptions);
    }

    /** @test */
    public function it_knows_of_authenticated_user_is_subscribed_to_it()
    {
        $thread = Thread::factory()->create();

        $this->actingAs(User::factory()->create());

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }


}
