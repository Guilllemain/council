<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    public function test_a_user_can_browse_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    public function test_a_user_can_view_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    public function test_a_user_can_read_replies()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->id]);

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
    }

    public function test_a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = $this->thread;

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    public function test_a_user_can_filter_threads_by_username()
    {
        $this->signIn(create('App\User', ['name' => 'John']));

        $threadbyJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = $this->thread;

        $this->get('/threads?by=John')
            ->assertSee($threadbyJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    public function test_a_user_can_filter_threads_by_popularity()
    {
        $threadWithOneReply = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithOneReply->id]);
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);
        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('/threads?popular=1')->json();

        $this->assertEquals([2, 1, 0], array_column($response['data'], 'replies_count'));
    }

    public function test_a_user_can_request_all_replies_for_a_given_thread()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->id]);

        $response = $this->getJson($this->thread->path())->json();

        $this->assertCount(1, $response['data']);
        $this->assertEquals(1, $response['total']);
    }

    public function test_a_user_can_filter_threads_by_those_who_have_no_replies()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('/threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }
}
