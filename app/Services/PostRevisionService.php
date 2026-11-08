<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DataTamperingException;
use App\Models\Post;
use App\Models\PostRevision;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostRevisionService
{
    public function __construct(
        private readonly TextDiffEngine $diffEngine = new TextDiffEngine
    ) {}

    public function createRevision(Post $post, User $actor, string $summary = 'Pembaruan konten'): ?PostRevision
    {
        /** @var PostRevision|null $latest */
        $latest = $post->revisions()->first();

        if ($latest !== null
            && $latest->title === $post->title
            && $latest->content === $post->content
            && $latest->excerpt === $post->excerpt
            && $latest->category_id === $post->category_id
            && $latest->status === $post->status
        ) {
            return null;
        }

        $nextNumber = ((int) $post->revisions()->max('revision_number')) + 1;
        $checksum = $this->diffEngine->computeChecksum(
            max(0, (int) $post->id),
            (string) $post->title,
            (string) $post->content,
            max(0, (int) $actor->id)
        );

        $revision = new PostRevision;
        $revision->post_id = max(0, (int) $post->id);
        $revision->user_id = max(0, (int) $actor->id);
        $revision->category_id = $post->category_id !== null ? max(0, (int) $post->category_id) : null;
        $revision->revision_number = max(0, $nextNumber);
        $revision->title = (string) $post->title;
        $revision->slug = (string) $post->slug;
        $revision->content = (string) $post->content;
        $revision->excerpt = $post->excerpt;
        $revision->status = (string) $post->status;
        $revision->change_summary = $summary;
        $revision->checksum = $checksum;
        $revision->save();

        $this->pruneOldRevisions($post);

        return $revision;
    }

    public function verifyIntegrity(PostRevision $revision): bool
    {
        $expectedChecksum = $this->diffEngine->computeChecksum(
            max(0, (int) $revision->post_id),
            (string) $revision->title,
            (string) $revision->content,
            max(0, (int) $revision->user_id)
        );

        return hash_equals($expectedChecksum, $revision->checksum);
    }

    public function rollback(Post $post, PostRevision $revision, User $actor): Post
    {
        if (! $this->verifyIntegrity($revision)) {
            throw DataTamperingException::forRevision((int) $revision->id);
        }

        return DB::transaction(function () use ($post, $revision, $actor) {
            $post->title = $revision->title;
            $post->slug = $revision->slug;
            $post->content = $revision->content;
            $post->excerpt = $revision->excerpt;
            $post->category_id = $revision->category_id;
            $post->status = $revision->status;
            $post->save();

            $this->createRevision(
                $post,
                $actor,
                "Rollback ke versi #{$revision->revision_number}"
            );

            return $post;
        });
    }

    public function pruneOldRevisions(Post $post, int $keepCount = 15): int
    {
        $revisionIdsToKeep = $post->revisions()
            ->orderByDesc('revision_number')
            ->limit($keepCount)
            ->pluck('id')
            ->all();

        if (empty($revisionIdsToKeep)) {
            return 0;
        }

        return $post->revisions()
            ->whereNotIn('id', $revisionIdsToKeep)
            ->delete();
    }
}