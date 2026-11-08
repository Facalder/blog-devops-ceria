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
 */
class Comments extends Component
{
    public Post $post;

    public string $authorName = '';

    public string $authorEmail = '';

    public string $content = '';

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

        $this->content = '';
        $this->feedbackMessage = 'Komentar Anda berhasil dikirim.';
        unset($this->comments);
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
        unset($this->comments);
    }

    public function delete(int $commentId): void
    {
        $comment = Comment::query()
            ->where('post_id', $this->post->id)
            ->findOrFail($commentId);

        if ($this->editingCommentId === $commentId) {
            $this->cancelEdit();
        }

        $comment->delete();
        $this->feedbackMessage = 'Komentar berhasil dihapus.';
        unset($this->comments);
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
        return Comment::query()
            ->where('post_id', $this->post->id)
            ->with('user')
            ->latest()
            ->get();
    }

    public function render(): View
    {
        return view('livewire.blog.comments');
    }

    private function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
