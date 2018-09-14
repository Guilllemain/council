<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    public function test_a_notification_is_prepared_when_a_thread_received_a_new_reply()
    {
        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Bla bla again'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Bla bla bla'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    public function test_a_user_can_fetch_their_unread_notifications()
    {
        create('Illuminate\Notifications\DatabaseNotification');

        $response = $this->getJson('/profiles/' . auth()->user()->name . '/notifications')->json();

        $this->assertCount(1, $response);
    }

    public function test_a_user_can_mark_a_notification_as_read()
    {
        create('Illuminate\Notifications\DatabaseNotification');

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $notificationId = auth()->user()->unreadNotifications->first()->id;

        $this->delete('/profiles/' . auth()->user()->name . '/notifications/' . $notificationId);

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
