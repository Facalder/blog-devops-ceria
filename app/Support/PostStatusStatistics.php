<?php

namespace App\Support;

class PostStatusStatistics
{
    /**
     * @param  array<int, array{status?: string}>  $posts
     * @return array{total: int, published: int, draft: int}
     */
    public static function countByStatus(array $posts): array
    {
        $result = [
            'total' => count($posts),
            'published' => 0,
            'draft' => 0,
        ];

        foreach ($posts as $post) {
            $status = $post['status'] ?? null;

            if ($status === 'published') {
                $result['published']++;
            } elseif ($status === 'draft') {
                $result['draft']++;
            }
        }

        return $result;
    }
}
