<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_favorite_anything()
    {
        $this->post('/replies/2/favorites')->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());
        $reply = Reply::factory()->create();

        $this->post('/replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_may_only_favorite_reply_once()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());
        $reply = Reply::factory()->create();

        $this->post('/replies/'. $reply->id . '/favorites');
        $this->post('/replies/'. $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }
}
