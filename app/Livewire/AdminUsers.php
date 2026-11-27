<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $role = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setRole(string $role): void
    {
        if ($role !== 'all' && UserRole::tryFrom($role) === null) {
            return;
        }

        $this->role = $role;
        $this->resetPage();
    }

    /**
     * Change a user's role. Admins cannot demote themselves.
     */
    public function updateRole(int $userId, string $role): void
    {
        $user = User::query()->findOrFail($userId);
        Gate::authorize('updateRole', $user);

        if ($user->id === auth()->id()) {
            $this->addError('role-'.$user->id, 'Нельзя изменить собственную роль.');

            return;
        }

        $newRole = UserRole::tryFrom($role);

        if ($newRole === null) {
            return;
        }

        $user->update(['role' => $newRole]);

        $this->dispatch('notify', message: 'Роль пользователя обновлена: '.$newRole->label());
    }

    public function render(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('shop')
            ->withCount(['orders', 'sellerRequests'])
            ->when($this->role !== 'all', fn ($query) => $query->where('role', $this->role))
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-users', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }
}
