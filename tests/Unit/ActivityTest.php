<?php

namespace Tests\Unit;


use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_records_activity_when_new_thread_is_created()
    {
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->assertDatabaseHas('activities',[
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Models\Thread'
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id,$thread->id);
    }

    /** @test */
    public function it_records_activity_when_reply_is_created()
    {
        $this->actingAs(User::factory()->create());

        $reply = Reply::factory()->create();

        $this->assertEquals(2,Activity::count());
    }

    /** @test */
    public function it_fetches_a_feed_for_any_user()
    {
        $this->actingAs(User::factory()->create());

        Thread::factory()->times(2)->create(['user_id' => auth()->id()]);

        auth()->user()->activity()->first()->update(['created_at' => now()->subWeek()]);

        $feed = Activity::feed(auth()->user(), 50);

        $this->assertTrue($feed->keys()->contains(
            now()->format('Y-m-d')
        ));

        $this->assertTrue($feed->keys()->contains(
            now()->subWeek()->format('Y-m-d')
        ));
    }

}
