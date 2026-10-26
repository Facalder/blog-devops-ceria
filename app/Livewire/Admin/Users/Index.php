<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Management User')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public ?int $editingUserId = null;

    public string $selectedRole = 'writer';

    public bool $showModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function editRole(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->selectedRole = $user->role;
        $this->showModal = true;
    }

    public function updateRole(): void
    {
        $this->validate([
            'selectedRole' => ['required', 'string', 'in:writer,admin,user'],
        ]);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->role = $this->selectedRole;
            $user->save();

            session()->flash('status', "Role user {$user->name} berhasil diperbarui.");
        }

        $this->showModal = false;
        $this->editingUserId = null;
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->withCount('posts')
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $sub): void {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter !== '', function (Builder $query): void {
                $query->where('role', $this->roleFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.admin.users.index');
    }
}
