<?php

namespace App\Http\Controllers;

use App\Reply;

class BestRepliesController extends Controller
{
    public function store(Reply $reply)
    {
        $this->authorize('update', $reply->thread);
        //same as :
        // abort_if($reply->thread->user_id != auth()->id(), 403);

        $reply->thread->markBestReply($reply);
    }
}
