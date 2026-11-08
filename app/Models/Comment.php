<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $post_id
 * @property int|null $parent_id
 * @property int|null $reply_to_id
 * @property int|null $user_id
 * @property string $author_name
 * @property string $author_email
 * @property string $content
 * @property int $depth
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User|null $user
 * @property-read Comment|null $parent
 * @property-read Comment|null $replyTo
 * @property-read Collection<int, Comment> $replies
 *
 * @mixin Builder<Comment>
 */
#[Fillable(['post_id', 'parent_id', 'reply_to_id', 'user_id', 'author_name', 'author_email', 'content'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    public int $depth = 0;

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
     * @return BelongsTo<Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * @return BelongsTo<Comment, $this>
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'reply_to_id');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function initials(): string
    {
        $initials = Str::initials($this->author_name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
