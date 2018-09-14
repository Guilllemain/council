<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    public function test_a_thread_requires_a_body_and_a_title_to_be_updated()
    {
        $this->withExceptionHandling();

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

        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }
}
