<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BookmarkButton extends Component
{
    public Post $post;

    public bool $bookmarked = false;

    public function mount(Post $post): void
    {
        $this->post = $post;

        $user = auth()->user();
        $this->bookmarked = $user !== null && $user->isBookmarked($post);
    }

    public function toggle(): void
    {
        $user = auth()->user();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $this->bookmarked = $user->toggleBookmark($this->post);
    }

    public function render(): View
    {
        return view('livewire.bookmark-button');
    }
}
