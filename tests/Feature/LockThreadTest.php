<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LockThreadTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_non_administrators_can_not_lock_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('lock-thread.store', $thread))->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    public function test_administrators_can_lock_threads()
    {
        $admin = factory('App\User')->states('administrator')->create();

        $this->signIn($admin);

        $thread = create('App\Thread');

        $this->post(route('lock-thread.store', $thread));

        $this->assertTrue(!! $thread->fresh()->locked);
    }

    public function test_once_locked_a_thread_may_not_received_new_replies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->update(['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'this is it',
            'user_id' => auth()->id(),
        ])->assertStatus(422);
    }
}
