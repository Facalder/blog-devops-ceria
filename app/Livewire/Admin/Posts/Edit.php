<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Artikel')]
#[Layout('layouts.app')]
class Edit extends Component
{
    public Post $post;

    public string $title = '';

    public string $slug = '';

    public ?int $category_id = null;

    public string $content = '';

    public ?string $excerpt = null;

    public string $status = 'draft';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->category_id = $post->category_id;
        $this->content = $post->content;
        $this->excerpt = $post->excerpt;
        $this->status = $post->status;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($this->post->id)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
        ];
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

        $this->post->title = $validated['title'];
        $this->post->slug = $validated['slug'];
        $this->post->category_id = $validated['category_id'] ? (int) $validated['category_id'] : null;
        $this->post->content = $validated['content'];
        $this->post->excerpt = $validated['excerpt'] ?: Str::limit(strip_tags($validated['content']), 160);

        if ($this->post->status !== 'published' && $validated['status'] === 'published') {
            $this->post->published_at = now();
        } elseif ($validated['status'] === 'draft') {
            $this->post->published_at = null;
        }

        $this->post->status = $validated['status'];
        $this->post->save();

        session()->flash('status', 'Artikel berhasil diperbarui.');

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
        return view('livewire.admin.posts.edit');
    }
}
