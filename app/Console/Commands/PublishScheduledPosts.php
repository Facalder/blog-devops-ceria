<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Terbitkan artikel terjadwal yang sudah mencapai waktu publikasi';

    public function handle(): int
    {
        $publishedCount = 0;

        foreach (Post::query()->dueForPublishing()->lazyById(100) as $post) {
            $publishedCount += $this->publish($post);
        }

        $this->info("{$publishedCount} artikel terjadwal berhasil diterbitkan.");

        return self::SUCCESS;
    }

    private function publish(Post $post): int
    {
        return Post::query()
            ->whereKey($post->getKey())
            ->where('status', 'scheduled')
            ->update([
                'status' => 'published',
                'published_at' => $post->scheduled_at,
                'scheduled_at' => null,
            ]);
    }
}
