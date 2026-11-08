<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
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

    public ?string $scheduled_at = null;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->category_id = $post->category_id;
        $this->content = $post->content;
        $this->excerpt = $post->excerpt;
        $this->status = $post->status;
        $this->scheduled_at = $post->scheduled_at?->format('Y-m-d\TH:i');
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
            'status' => ['required', 'in:draft,published,scheduled'],
            'scheduled_at' => $this->status === 'scheduled'
                ? ['required', 'date', 'after:now']
                : ['nullable'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'scheduled_at.required' => 'Tanggal dan waktu publikasi wajib diisi untuk artikel terjadwal.',
            'scheduled_at.date' => 'Format tanggal dan waktu publikasi tidak valid.',
            'scheduled_at.after' => 'Jadwal publikasi harus berada di masa depan.',
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
        $status = $validated['status'];

        $this->post->title = $validated['title'];
        $this->post->slug = $validated['slug'];
        $this->post->category_id = $validated['category_id'] ? (int) $validated['category_id'] : null;
        $this->post->content = $validated['content'];
        $this->post->excerpt = $validated['excerpt'] ?: Str::limit(strip_tags($validated['content']), 160);

        if ($status === 'published') {
            if ($this->post->status !== 'published' || $this->post->published_at === null) {
                $this->post->published_at = now();
            }

            $this->post->scheduled_at = null;
        } elseif ($status === 'scheduled') {
            $this->post->published_at = null;
            $this->post->scheduled_at = Carbon::parse($validated['scheduled_at']);
        } else {
            $this->post->published_at = null;
            $this->post->scheduled_at = null;
        }

        $this->post->status = $status;
        $this->post->save();

        session()->flash('status', 'Artikel berhasil diperbarui.');

        $this->redirect(route('posts.index'), navigate: true);
    }

    public function cancelSchedule(): void
    {
        if ($this->post->status !== 'scheduled') {
            return;
        }

        $this->post->status = 'draft';
        $this->post->scheduled_at = null;
        $this->post->save();

        $this->status = 'draft';
        $this->scheduled_at = null;

        session()->flash('status', 'Jadwal publikasi dibatalkan. Artikel kembali menjadi draft.');

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
