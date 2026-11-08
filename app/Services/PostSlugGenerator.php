<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostSlugGenerator
{
    private const MAX_BASE_LENGTH = 240;

    private const FALLBACK_SLUG = 'artikel';

    public function generate(string $title, ?int $ignorePostId = null): string
    {
        $base = $this->baseSlug($title);

        $taken = Post::query()
            ->when($ignorePostId, fn ($query, int $id) => $query->whereKeyNot($id))
            ->where(function ($query) use ($base) {
                $query->where('slug', $base)
                    ->orWhere('slug', 'like', $base.'-%');
            })
            ->pluck('slug');

        if (! $taken->contains($base)) {
            return $base;
        }

        $suffix = 2;

        while ($taken->contains("{$base}-{$suffix}")) {
            $suffix++;
        }

        return "{$base}-{$suffix}";
    }

    private function baseSlug(string $title): string
    {
        $slug = rtrim(substr(Str::slug($title), 0, self::MAX_BASE_LENGTH), '-');

        return $slug !== '' ? $slug : self::FALLBACK_SLUG;
    }
}
