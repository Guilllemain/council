<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Thread;
use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function show(User $user)
    {
        $profileUser = $user;
        $activities = Activity::feed($user);

        return view('profiles.show', compact('profileUser', 'activities'));
    }
}
