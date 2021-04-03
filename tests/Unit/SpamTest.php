<?php

namespace Tests\Unit;

use App\Models\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validates_spam()
    {
         $spam = new Spam();

         $this->assertFalse($spam->detect('Innocent reply here'));
    }
}
