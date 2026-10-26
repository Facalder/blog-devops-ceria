<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tulis Artikel Baru')]
#[Layout('layouts.app')]
class Create extends Component
{
    public string $title = '';

    public string $slug = '';

    public ?int $category_id = null;

    public string $content = '';

    public ?string $excerpt = null;

    public string $status = 'draft';

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts,slug'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    public function updatedTitle(string $value): void
    {
        if ($this->slug === '' || $this->slug === Str::slug(Str::beforeLast($value, ' '))) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        if (empty($this->slug) && ! empty($this->title)) {
            $this->slug = Str::slug($this->title);
        }

        if (empty($this->category_id)) {
            $this->category_id = null;
        }

        $validated = $this->validate();

        $post = new Post;
        $post->user_id = (int) Auth::id();
        $post->title = $validated['title'];
        $post->slug = $validated['slug'];
        $post->category_id = $validated['category_id'] ? (int) $validated['category_id'] : null;
        $post->content = $validated['content'];
        $post->excerpt = $validated['excerpt'] ?: Str::limit(strip_tags($validated['content']), 160);
        $post->status = $validated['status'];
        $post->published_at = $validated['status'] === 'published' ? now() : null;
        $post->save();

        session()->flash('status', 'Artikel berhasil dibuat.');

        $this->redirect(route('posts.index'), navigate: true);
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
        return view('livewire.admin.posts.create');
    }
}
