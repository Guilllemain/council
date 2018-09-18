<?php

namespace App\Listeners;

use App\User;
use App\Events\ThreadHasNewReply;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadHasNewReply $event)
    {
        // same as foreach
        // User::whereIn('name', $event->reply->mentionedUsers())
        // ->get()
        // ->each(function ($user) {
        //     $user->notify(new YouWereMentioned($event->reply));
        // });
        $mentionedUsers = $event->reply->mentionedUsers();

        foreach ($mentionedUsers as $name) {
            $user = User::whereName($name)->first();

            if ($user) {
                $user->notify(new YouWereMentioned($event->reply));
            }
        }
    }
}
