<?php

namespace Tests\Unit;

use App\Support\PostStatusStatistics;
use PHPUnit\Framework\TestCase;

class PostStatusStatisticsTest extends TestCase
{
    public function test_count_posts_by_status(): void
    {
        $posts = [
            ['status' => 'published'],
            ['status' => 'draft'],
            ['status' => 'published'],
        ];

        $result = PostStatusStatistics::countByStatus($posts);

        $this->assertEquals(3, $result['total']);
        $this->assertEquals(2, $result['published']);
        $this->assertEquals(1, $result['draft']);
    }

    public function test_empty_posts(): void
    {
        $result = PostStatusStatistics::countByStatus([]);

        $this->assertEquals(0, $result['total']);
        $this->assertEquals(0, $result['published']);
        $this->assertEquals(0, $result['draft']);
    }

    public function test_all_posts_published(): void
    {
        $posts = [
            ['status' => 'published'],
            ['status' => 'published'],
        ];

        $result = PostStatusStatistics::countByStatus($posts);

        $this->assertEquals(2, $result['published']);
        $this->assertEquals(0, $result['draft']);
    }

    public function test_all_posts_draft(): void
    {
        $posts = [
            ['status' => 'draft'],
            ['status' => 'draft'],
        ];

        $result = PostStatusStatistics::countByStatus($posts);

        $this->assertEquals(0, $result['published']);
        $this->assertEquals(2, $result['draft']);
    }

    public function test_unknown_status(): void
    {
        $posts = [
            ['status' => 'archived'],
            ['status' => 'published'],
        ];

        $result = PostStatusStatistics::countByStatus($posts);

        $this->assertEquals(2, $result['total']);
        $this->assertEquals(1, $result['published']);
        $this->assertEquals(0, $result['draft']);
    }
}
