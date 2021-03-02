<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
}
