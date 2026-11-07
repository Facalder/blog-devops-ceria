<?php

namespace App\Livewire\Blog;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Comments extends Component
{
    public Post $post;

    public string $authorName = '';

    public string $authorEmail = '';

    public string $content = '';

    public ?string $feedbackMessage = null;

    public function mount(Post $post): void
    {
        $this->post = $post;

        if (auth()->check()) {
            $user = auth()->user();
            $this->authorName = $user->name;
            $this->authorEmail = $user->email;
        }
    }

    public function save(): void
    {
        if (auth()->check()) {
            $this->validate([
                'content' => ['required', 'string', 'min:3', 'max:2000'],
            ]);

            $authorName = auth()->user()->name;
            $authorEmail = auth()->user()->email;
            $userId = auth()->id();
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

        $this->post->comments()->create([
            'user_id' => $userId,
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => trim($this->content),
        ]);

        $this->content = '';
        $this->feedbackMessage = 'Komentar Anda berhasil dikirim.';
        unset($this->comments);
    }

    public function delete(int $commentId): void
    {
        $comment = $this->post->comments()->findOrFail($commentId);

        $canDelete = auth()->check() && (
            auth()->user()->isAdmin() ||
            auth()->id() === $this->post->user_id ||
            auth()->id() === $comment->user_id
        );

        abort_unless($canDelete, 403);

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
        return $this->post->comments()
            ->with('user')
            ->latest()
            ->get();
    }

    public function render(): View
    {
        return view('livewire.blog.comments');
    }
}
