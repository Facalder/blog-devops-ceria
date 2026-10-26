<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Management Kategori')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public ?int $editingCategoryId = null;

    public bool $showModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedName(string $value): void
    {
        if ($this->slug === '' || $this->slug === Str::slug(Str::beforeLast($value, ' '))) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingCategoryId']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->editingCategoryId),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update($validated);
            session()->flash('status', 'Kategori berhasil diperbarui.');
        } else {
            Category::create($validated);
            session()->flash('status', 'Kategori baru berhasil dibuat.');
        }

        $this->showModal = false;
        $this->reset(['name', 'slug', 'description', 'editingCategoryId']);
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->delete();

        session()->flash('status', 'Kategori berhasil dihapus.');
    }

    /**
     * @return LengthAwarePaginator<int, Category>
     */
    #[Computed]
    public function categories(): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('posts')
            ->when($this->search !== '', function (Builder $query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.admin.categories.index');
    }
}
