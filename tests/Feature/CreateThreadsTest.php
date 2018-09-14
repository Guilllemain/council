<?php

namespace Tests\Feature;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            $m = \Mockery::mock(Recaptcha::class);
            $m->shouldReceive('passes')->andReturn(true);
            return $m;
        });
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_guest_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('login');
            
        $this->post('/threads')
            ->assertRedirect('login');
    }

    public function test_an_authenticated_user_can_create_new_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');

        $response = $this->post('/threads', $thread->toArray() + ['g-recaptcha-response' => 'test']);
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    public function test_a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function test_a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    public function test_a_thread_requires_a_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    public function test_a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Help Me']);

        $this->assertEquals($thread->fresh()->slug, 'help-me');

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("help-me-{$thread['id']}", $thread['slug']);
    }

    public function test_a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
    }

    public function test_a_thread_requires_a_body_and_a_title_to_be_updated()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'changed',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'changed',
        ])->assertSessionHasErrors('title');
    }

    public function test_a_thread_can_be_updated_by_its_creator()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'changed',
            'body' => 'changed body'
        ]);

        $this->assertEquals('changed', $thread->fresh()->title);
        $this->assertEquals('changed body', $thread->fresh()->body);
    }

    public function test_unauthorized_user_cannot_update_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);

        $this->patch($thread->path(), [
            'title' => 'changed',
            'body' => 'changed body'
        ])->assertStatus(403);
    }

    public function test_an_authenticated_user_must_confirm_their_email_before_create_new_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();
        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post('/threads', $thread->toArray())
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');
    }

    public function test_a_thread_can_be_deleted_by_authorized_users()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path());

        $this->assertEquals(0, \App\Activity::count());
    }

    public function test_unauthorized_user_cannot_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())
            ->assertStatus(403);
    }

    protected function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);
        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }
}
