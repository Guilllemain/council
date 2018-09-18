<?php

namespace App\Http\Controllers;

use App\User;
use App\Activity;

class ProfilesController extends Controller
{
    public function show(User $user)
    {
        $profileUser = $user;
        $activities = Activity::feed($user);

        return view('profiles.show', compact('profileUser', 'activities'));
    }
}
