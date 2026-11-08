<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Services\PostExcerptGenerator;
use App\Services\PostSlugGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tulis Artikel Baru')]
#[Layout('layouts.app')]
class Create extends Component
{
    public string $title = '';

    public string $slug = '';

    #[Locked]
    public bool $slugEdited = false;

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
        if (! $this->slugEdited) {
            $this->slug = trim($value) === ''
                ? ''
                : app(PostSlugGenerator::class)->generate($value);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugEdited = trim($this->slug) !== '';
    }

    public function save(): void
    {
        if (! $this->slugEdited && trim($this->title) !== '') {
            $this->slug = app(PostSlugGenerator::class)->generate($this->title);
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
        $post->excerpt = $validated['excerpt'] ?: app(PostExcerptGenerator::class)->generate($validated['content']);
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
