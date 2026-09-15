<?php
/**
 * @author denis.chernonozhkin
 * @Date 15.09.2026 16:28
 */

namespace App\Livewire;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FollowCount extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    #[On('follow-toggled')]
    public function refreshCount(): void
    {
        // Перезагружаем компонент
        $this->user->refresh();
    }

    public function render(): View
    {
        return view('livewire.follow-count', [
            'followersCount' => $this->user->followers()->count(),
            'followingsCount' => $this->user->followings()->count(),
        ]);
    }
}
