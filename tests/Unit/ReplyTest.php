<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    public function test_it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');
        
        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    public function test_it_can_detect_all_mentioned_users_in_the_body()
    {
        // faster than create('App\Reply')
        $reply = new \App\Reply([
            'body' => '@Jane wants to talk to @John'
        ]);

        $this->assertEquals(['Jane', 'John'], $reply->mentionedUsers());
    }

    public function test_it_wraps_mentioned_usernames_in_the_body_within_anchor_tags()
    {
        $reply = new \App\Reply([
            'body' => 'Hello @Jane-Doe.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.',
            $reply->body
        );
    }

    public function test_it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update([
            'best_reply_id' => $reply->id
        ]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    public function test_a_reply_body_is_sanitized_automatically()
    {
        $reply = make('App\Reply', ['body' => '<script>alert("bad")</script><p>This is ok</p>']);

        $this->assertEquals('<p>This is ok</p>', $reply->body);
    }
}
