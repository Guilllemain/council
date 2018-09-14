<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ThreadTest extends TestCase
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

    public function test_a_thread_has_a_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals('/threads/' . $thread->channel->slug . '/' . $thread->slug, $thread->path());
    }

    public function test_a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    public function test_a_thread_has_a_creater()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    public function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    public function test_a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    public function test_a_thread_can_be_subscribed_to()
    {
        $thread = create('App\Thread');

        $thread->subscribe($userId = 1);

        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    public function test_a_thread_can_be_unsubscribe_from()
    {
        $this->thread->subscribe($userId = 1);

        $this->thread->unsubscribe($userId);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    public function test_a_thread_knows_if_an_authenticated_user_has_subscribed_to_it()
    {
        $this->signIn();

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    public function test_a_thread_notifies_all_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn();

        $this->thread->subscribe();

        $this->thread->addReply([
            'body' => 'FooBar',
            'user_id' => 1
        ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    public function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $this->assertTrue($this->thread->hasUpdatesFor(auth()->user()));

        auth()->user()->read($this->thread);

        $this->assertFalse($this->thread->hasUpdatesFor(auth()->user()));
    }

    public function test_a_thread_records_each_visit()
    {
        $thread = make('App\Thread', ['id' => 1]);

        Redis::del('threads' . $thread->id . 'visits');

        $this->assertSame(0, $thread->visits()->count($thread));

        $thread->visits()->record($thread);

        $this->assertEquals(1, $thread->visits()->count($thread));

        $thread->visits()->record($thread);

        $this->assertEquals(2, $thread->visits()->count($thread));
    }

    public function test_a_thread_body_is_sanitized_automatically()
    {
        $thread = make('App\Thread', ['body' => '<script>alert("bad")</script><p>This is ok</p>']);

        $this->assertEquals('<p>This is ok</p>', $thread->body);
    }
}
