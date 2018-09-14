<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_an_unauthenticated_user_may_not_participate_in_forum_threads()
    {
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('login');
    }

    public function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply');
        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
    }

    public function test_a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => null]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    public function test_unauthorized_users_may_not_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->delete('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->delete('/replies/' . $reply->id)
            ->assertStatus(403);
    }

    public function test_authorized_users_can_delete_replies()
    {
        $this->withExceptionHandling()->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete('/replies/' . $reply->id)
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', $reply->toArray());
    }

    public function test_authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->patch('/replies/' . $reply->id, ['body' => 'You been changed']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'You been changed']);
    }

    public function test_unauthorized_users_may_not_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->patch('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->patch('/replies/' . $reply->id)
            ->assertStatus(403);
    }

    public function test_replies_that_contains_spam_may_not_be_created()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    public function test_users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => 'simple']);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
