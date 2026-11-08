<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostRevision;
use App\Models\User;
use App\Services\PostExcerptGenerator;
use App\Services\PostRevisionService;
use App\Services\PostSlugGenerator;
use App\Services\TextDiffEngine;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public bool $showRevisionModal = false;

    public ?int $selectedRevisionId = null;

    public string $diffHtml = '';

    public string $diffStats = '';

    public bool $confirmingRestore = false;

    public ?int $selectedRevisionNumber = null;

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

        if ($post->revisions()->count() === 0) {
            $author = $post->user;
            (new PostRevisionService)->createRevision($post, $author, 'Inisialisasi baseline artikel');
        }
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
        if (trim($this->slug) === '' && trim($this->title) !== '') {
            $this->slug = app(PostSlugGenerator::class)->generate($this->title, $this->post->id);
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
        $this->post->excerpt = $validated['excerpt'] ?: app(PostExcerptGenerator::class)->generate($validated['content']);

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

        /** @var User $actor */
        $actor = Auth::user();
        (new PostRevisionService)->createRevision($this->post, $actor, 'Pembaruan artikel');

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
        $this->post->published_at = null;
        $this->post->save();

        /** @var User $actor */
        $actor = Auth::user();
        (new PostRevisionService)->createRevision($this->post, $actor, 'Pembatalan jadwal publikasi');

        session()->flash('status', 'Jadwal publikasi artikel berhasil dibatalkan.');

        $this->redirect(route('posts.index'), navigate: true);
    }

    public function viewDiff(int $revisionId): void
    {
        /** @var PostRevision|null $revision */
        $revision = $this->post->revisions()->where('id', $revisionId)->first();

        if ($revision === null) {
            return;
        }

        $this->selectedRevisionId = $revisionId;
        $diffEngine = new TextDiffEngine;
        $diff = $diffEngine->calculateDiff($revision->content, $this->content);
        $this->diffHtml = $diffEngine->renderHtml($diff);

        $stats = $diffEngine->getStatistics($diff);
        $this->diffStats = "+{$stats['added']} karakter ditambah, -{$stats['removed']} karakter dihapus";
        $this->showRevisionModal = true;
    }

    public function closeDiff(): void
    {
        $this->showRevisionModal = false;
        $this->selectedRevisionId = null;
        $this->diffHtml = '';
        $this->diffStats = '';
    }

    public function restoreRevision(int $revisionId): void
    {
        /** @var PostRevision|null $revision */
        $revision = $this->post->revisions()->where('id', $revisionId)->first();

        if ($revision === null) {
            return;
        }

        /** @var User $actor */
        $actor = Auth::user();

        (new PostRevisionService)->rollback($this->post, $revision, $actor);

        $this->post->refresh();
        $this->title = (string) $this->post->title;
        $this->slug = (string) $this->post->slug;
        $this->content = (string) $this->post->content;
        $this->excerpt = $this->post->excerpt;
        $this->category_id = $this->post->category_id ? (int) $this->post->category_id : null;
        $this->status = (string) $this->post->status;

        $this->closeDiff();

        session()->flash('status', "Berhasil memulihkan artikel ke Versi #{$revision->revision_number}.");
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    /**
     * @return Collection<int, PostRevision>
     */
    /**
     * @return Collection<int, PostRevision>
     */
    #[Computed]
    public function revisions(): Collection
    {
        /** @var Collection<int, PostRevision> $result */
        $result = $this->post->revisions()->with('user')->get();

        return $result;
    }

    public function render(): View
    {
        return view('livewire.admin.posts.edit');
    }

    public function startRollbackFromSidebar(int $revisionId): void
    {
        $this->viewDiff($revisionId);
        $this->confirmingRestore = true;
    }
}
