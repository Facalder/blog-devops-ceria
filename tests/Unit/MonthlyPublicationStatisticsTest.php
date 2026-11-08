<?php

namespace Tests\Unit;

use App\Support\MonthlyPublicationStatistics;
use PHPUnit\Framework\TestCase;

class MonthlyPublicationStatisticsTest extends TestCase
{
    public function test_count_posts_by_month(): void
    {
        $posts = [
            ['status' => 'published', 'published_at' => '2026-01-10'],
            ['status' => 'published', 'published_at' => '2026-01-20'],
            ['status' => 'published', 'published_at' => '2026-02-05'],
        ];

        $result = MonthlyPublicationStatistics::countByMonth($posts, 2026);

        $this->assertEquals(2, $result[1]);
        $this->assertEquals(1, $result[2]);
        $this->assertEquals(0, $result[3]);
    }

    public function test_empty_posts(): void
    {
        $result = MonthlyPublicationStatistics::countByMonth([], 2026);

        $this->assertCount(12, $result);
        $this->assertEquals(0, array_sum($result));
    }

    public function test_draft_posts_are_not_counted(): void
    {
        $posts = [
            ['status' => 'draft', 'published_at' => '2026-03-10'],
        ];

        $result = MonthlyPublicationStatistics::countByMonth($posts, 2026);

        $this->assertEquals(0, $result[3]);
    }

    public function test_posts_without_publication_date(): void
    {
        $posts = [
            ['status' => 'published', 'published_at' => null],
        ];

        $result = MonthlyPublicationStatistics::countByMonth($posts, 2026);

        $this->assertEquals(0, array_sum($result));
    }

    public function test_only_selected_year_is_counted(): void
    {
        $posts = [
            ['status' => 'published', 'published_at' => '2025-01-10'],
            ['status' => 'published', 'published_at' => '2026-01-15'],
        ];

        $result = MonthlyPublicationStatistics::countByMonth($posts, 2026);

        $this->assertEquals(1, $result[1]);
    }
}
