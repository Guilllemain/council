<?php

namespace App\Listeners;

use App\Mail\PleaseConfirmEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class SendEmailConfirmation
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
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        Mail::to($event->user)->send(new PleaseConfirmEmail($event->user));
    }
}
