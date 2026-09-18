<?php
/**
 * @author denis.chernonozhkin
 * @Date 18.09.2026 18:45
 */

namespace App\Livewire;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserRoleSelect extends Component
{
    public User $user;
    public bool $isOpen = false;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function toggle(): void
    {
        // Если пользователь — vortex_admin, не открываем список
        if ($this->user->hasRole('vortex_admin')) {
            return;
        }

        $this->isOpen = !$this->isOpen;
    }

    public function changeRole(int $roleId): void
    {
        // Защита: нельзя менять роль vortex_admin
        if ($this->user->hasRole('vortex_admin')) {
            abort(403);
        }

        $role = Role::findOrFail($roleId);

        // Защита: нельзя назначить vortex_admin
        if ($role->name === 'vortex_admin') {
            abort(403);
        }

        // Синхронизируем роли (только одна роль)
        $this->user->syncRoles([$role->name]);

        $this->isOpen = false;
        $this->dispatch('role-changed');
    }

    public function render(): View
    {
        // Список ролей, кроме vortex_admin
        $roles = Role::where('name', '!=', 'vortex_admin')->get();

        return view('livewire.user-role-select', [
            'roles' => $roles,
        ]);
    }
}
