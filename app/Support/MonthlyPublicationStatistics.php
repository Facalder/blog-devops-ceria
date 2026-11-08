<?php

namespace App\Support;

use Carbon\Carbon;

class MonthlyPublicationStatistics
{
    /**
     * @param array<int, array{
     *     status?: string,
     *     published_at?: string|null
     * }> $posts
     * @return array<int, int>
     */
    public static function countByMonth(array $posts, int $year): array
    {
        $result = array_fill(1, 12, 0);

        foreach ($posts as $post) {
            if (($post['status'] ?? null) !== 'published') {
                continue;
            }

            $publishedAt = $post['published_at'] ?? null;

            if ($publishedAt === null) {
                continue;
            }

            $date = Carbon::parse($publishedAt);

            if ($date->year === $year) {
                $result[$date->month]++;
            }
        }

        return $result;
    }
}
