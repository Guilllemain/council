<?php

namespace App\Http\Controllers;

use App\Thread;

class LockedThreadsController extends Controller
{
    public function store(Thread $thread)
    {
        if ($thread->locked == false) {
            $thread->update(['locked' => true]);
        } else {
            $thread->update(['locked' => false]);
        }
    }
}
