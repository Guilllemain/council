<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        Mail::assertQueued(PleaseConfirmEmail::class);
    }

    public function test_user_can_confirm_their_email_address()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $user = \App\User::whereName('Jane')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get('/register/confirm?token=' . $user->confirmation_token)
            ->assertRedirect('/threads');

        $this->assertTrue($user->fresh()->confirmed);
        $this->assertNull($user->fresh()->confirmation_token);
    }

    public function test_confirming_an_invalid_token()
    {
        $this->get('/register/confirm?token=invalid')
            ->assertRedirect('/threads')
            ->assertSessionHas('flash', 'Invalid token');
    }
}
