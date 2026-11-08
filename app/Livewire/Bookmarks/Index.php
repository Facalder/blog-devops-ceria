<?php

namespace App\Livewire\Bookmarks;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Bookmark Saya')]
#[Layout('layouts.app')]
class Index extends Component
{
    public function remove(int $postId): void
    {
        $user = auth()->user();

        if ($user === null) {
            abort(401);
        }

        $user->bookmarks()->detach($postId);

        session()->flash('status', 'Artikel dihapus dari bookmark.');
    }

    /**
     * @return Collection<int, Post>
     */
    #[Computed]
    public function posts(): Collection
    {
        $user = auth()->user();

        if ($user === null) {
            abort(401);
        }

        return $user->getBookmarkedPosts();
    }

    public function render(): View
    {
        return view('livewire.bookmarks.index');
    }
}
