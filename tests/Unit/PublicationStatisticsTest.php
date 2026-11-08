<?php

namespace Tests\Unit;

use App\Support\PublicationStatistics;
use PHPUnit\Framework\TestCase;

class PublicationStatisticsTest extends TestCase
{
    public function test_percentage_calculation(): void
    {
        $this->assertEquals(
            75.0,
            PublicationStatistics::percentage(15, 20)
        );
    }

    public function test_zero_articles(): void
    {
        $this->assertEquals(
            0.0,
            PublicationStatistics::percentage(0, 0)
        );
    }

    public function test_full_publication(): void
    {
        $this->assertEquals(
            100.0,
            PublicationStatistics::percentage(20, 20)
        );
    }

    public function test_percentage_cannot_exceed_100(): void
    {
        $this->assertEquals(
            100.0,
            PublicationStatistics::percentage(25, 20)
        );
    }

    public function test_publication_status(): void
    {
        $this->assertEquals('Rendah', PublicationStatistics::status(25));
        $this->assertEquals('Sedang', PublicationStatistics::status(60));
        $this->assertEquals('Tinggi', PublicationStatistics::status(90));
    }
}
