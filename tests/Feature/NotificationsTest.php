<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_current_user()
    {
        $thread = Thread::factory()->create()->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Reply body'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Reply body'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        $this->withoutExceptionHandling();

        DatabaseNotification::factory()->create();

        $this->assertCount(1,$this->getJson("/profiles/". auth()->user()->name . "/notifications")->json());

    }

    /** @test */
    public function a_user_can_mark_notification_as_read()
    {
        $this->withoutExceptionHandling();

        DatabaseNotification::factory()->create();

        tap(auth()->user(),function ($user){
            $this->assertCount(1,$user->unreadNotifications);

            $this->delete("/profiles/{$user->name}/notifications/". $user->unreadNotifications->first()->id);

            $this->assertCount(0,$user->fresh()->unreadNotifications);
        });
    }
}
