<?php

namespace App\Support;

class PublicationStatistics
{
    public static function percentage(
        int $published,
        int $total
    ): float {
        if ($total <= 0) {
            return 0.0;
        }

        return round(
            min(100, max(0, $published / $total * 100)),
            2
        );
    }

    public static function status(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'Tinggi';
        }

        if ($percentage >= 50) {
            return 'Sedang';
        }

        return 'Rendah';
    }
}
