<?php

namespace App\Livewire\Blog;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property Collection<int, Comment> $comments
 * @property int $totalCommentsCount
 * @property-read Comment|null $replyingToComment
 */
class Comments extends Component
{
    public Post $post;

    public string $authorName = '';

    public string $authorEmail = '';

    public string $content = '';

    public ?int $replyingToCommentId = null;

    public string $replyContent = '';

    public ?int $editingCommentId = null;

    public string $editingContent = '';

    public ?string $feedbackMessage = null;

    public function mount(Post $post): void
    {
        $this->post = $post;

        $user = $this->currentUser();
        if ($user !== null) {
            $this->authorName = $user->name;
            $this->authorEmail = $user->email;
        }
    }

    public function save(): void
    {
        $user = $this->currentUser();

        if ($user !== null) {
            $this->validate([
                'content' => ['required', 'string', 'min:3', 'max:2000'],
            ]);

            $authorName = $user->name;
            $authorEmail = $user->email;
            $userId = $user->id;
        } else {
            $this->validate([
                'authorName' => ['required', 'string', 'min:2', 'max:100'],
                'authorEmail' => ['required', 'email', 'max:255'],
                'content' => ['required', 'string', 'min:3', 'max:2000'],
            ]);

            $authorName = trim($this->authorName);
            $authorEmail = trim($this->authorEmail);
            $userId = null;
        }

        Comment::query()->create([
            'post_id' => $this->post->id,
            'user_id' => $userId,
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => trim($this->content),
        ]);

        $this->dispatch('profile-saved', name: $authorName, email: $authorEmail);

        $this->content = '';
        $this->feedbackMessage = 'Komentar Anda berhasil dikirim.';
        unset($this->comments, $this->totalCommentsCount);
    }

    public function startReply(int $commentId): void
    {
        $comment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($commentId);

        $this->replyingToCommentId = $comment->id;
        $this->replyContent = '';
        $this->resetValidation(['replyContent', 'authorName', 'authorEmail']);
        unset($this->replyingToComment);
    }

    public function cancelReply(): void
    {
        $this->replyingToCommentId = null;
        $this->replyContent = '';
        $this->resetValidation(['replyContent']);
        unset($this->replyingToComment);
    }

    public function saveReply(): void
    {
        abort_if($this->replyingToCommentId === null, 400);

        $targetComment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($this->replyingToCommentId);

        $parentId = $targetComment->id;
        $replyToId = $targetComment->id;

        $user = $this->currentUser();

        if ($user !== null) {
            $this->validate([
                'replyContent' => ['required', 'string', 'min:3', 'max:2000'],
            ]);

            $authorName = $user->name;
            $authorEmail = $user->email;
            $userId = $user->id;
        } else {
            $this->validate([
                'authorName' => ['required', 'string', 'min:2', 'max:100'],
                'authorEmail' => ['required', 'email', 'max:255'],
                'replyContent' => ['required', 'string', 'min:3', 'max:2000'],
            ]);

            $authorName = trim($this->authorName);
            $authorEmail = trim($this->authorEmail);
            $userId = null;
        }

        Comment::query()->create([
            'post_id' => $this->post->id,
            'parent_id' => $parentId,
            'reply_to_id' => $replyToId,
            'user_id' => $userId,
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => trim($this->replyContent),
        ]);

        $this->dispatch('profile-saved', name: $authorName, email: $authorEmail);

        $this->replyingToCommentId = null;
        $this->replyContent = '';
        $this->feedbackMessage = 'Balasan Anda berhasil dikirim.';
        unset($this->comments, $this->totalCommentsCount, $this->replyingToComment);
    }

    public function edit(int $commentId): void
    {
        $comment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($commentId);

        $this->editingCommentId = $comment->id;
        $this->editingContent = $comment->content;
        $this->resetValidation('editingContent');
    }

    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
        $this->resetValidation('editingContent');
    }

    public function update(): void
    {
        abort_if($this->editingCommentId === null, 400);

        $comment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($this->editingCommentId);

        $this->validate([
            'editingContent' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $comment->update([
            'content' => trim($this->editingContent),
        ]);

        $this->editingCommentId = null;
        $this->editingContent = '';
        $this->feedbackMessage = 'Komentar berhasil diperbarui.';
        unset($this->comments, $this->totalCommentsCount);
    }

    public function delete(int $commentId): void
    {
        $comment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($commentId);

        if ($this->editingCommentId === $commentId) {
            $this->cancelEdit();
        }

        if ($this->replyingToCommentId === $commentId) {
            $this->cancelReply();
        }

        $comment->delete();
        $this->feedbackMessage = 'Komentar berhasil dihapus.';
        unset($this->comments, $this->totalCommentsCount);
    }

    public function dismissFeedback(): void
    {
        $this->feedbackMessage = null;
    }

    /**
     * @return Collection<int, Comment>
     */
    #[Computed]
    public function comments(): Collection
    {
        $allComments = Comment::query()
            ->where('post_id', $this->post->id)
            ->with(['user', 'replyTo'])
            ->oldest()
            ->get();

        return $this->buildTree($allComments);
    }

    #[Computed]
    public function replyingToComment(): ?Comment
    {
        if ($this->replyingToCommentId === null) {
            return null;
        }

        return Comment::query()
            ->where('post_id', $this->post->id)
            ->with('user')
            ->find($this->replyingToCommentId);
    }

    #[Computed]
    public function totalCommentsCount(): int
    {
        return Comment::query()
            ->where('post_id', $this->post->id)
            ->count();
    }

    public function render(): View
    {
        return view('livewire.blog.comments');
    }

    /**
     * @param  Collection<int, Comment>  $comments
     * @return Collection<int, Comment>
     */
    private function buildTree(Collection $comments, ?int $parentId = null, int $depth = 0): Collection
    {
        $branch = new Collection;

        foreach ($comments as $comment) {
            if ($comment->parent_id === $parentId) {
                $comment->depth = $depth;
                $comment->setRelation('replies', $this->buildTree($comments, $comment->id, $depth + 1));
                $branch->push($comment);
            }
        }

        return $depth === 0 ? $branch->reverse() : $branch;
    }

    private function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
