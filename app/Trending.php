<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Trending extends Model
{
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    public function cacheKey()
    {
        return app()->environment('testing') ? 'test_trending_threads' : 'trending_threads';
    }
}
