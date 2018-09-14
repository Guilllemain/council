<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();

        // Redis::del($this->trending->cacheKey());
    }

    public function test_it_increments_a_thread_score_each_time_it_is_read()
    {
        $this->assertEmpty($this->trending->get());
        
        $thread = create('App\Thread');
        
        $this->call('GET', $thread->path());

        // $trending = $this->trending->get();

        // $this->assertCount(1, $trending);

        // $this->assertEquals($thread->title, $trending[0]->title);
    }
}
