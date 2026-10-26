<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Management Blog')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $categoryId = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        $post->delete();

        session()->flash('status', 'Artikel berhasil dihapus.');
    }

    /**
     * @return LengthAwarePaginator<int, Post>
     */
    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user', 'category'])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $sub): void {
                    $sub->where('title', 'like', "%{$this->search}%")
                        ->orWhere('excerpt', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', function (Builder $query): void {
                $query->where('status', $this->status);
            })
            ->when($this->categoryId !== '', function (Builder $query): void {
                $query->where('category_id', (int) $this->categoryId);
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.admin.posts.index');
    }
}
