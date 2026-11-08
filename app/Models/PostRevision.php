<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PostRevisionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int|null $category_id
 * @property int $revision_number
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $excerpt
 * @property string $status
 * @property string $change_summary
 * @property string $checksum
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User $user
 * @property-read Category|null $category
 */
#[Fillable([
    'post_id',
    'user_id',
    'category_id',
    'revision_number',
    'title',
    'slug',
    'content',
    'excerpt',
    'status',
    'change_summary',
    'checksum',
])]
class PostRevision extends Model
{
    /** @use HasFactory<PostRevisionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'revision_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param  Builder<PostRevision>  $query
     * @return Builder<PostRevision>
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('revision_number');
    }
}