<?php
/**
 * @author denis.chernonozhkin
 * @Date 15.09.2026 14:26
 */

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class FollowButton extends Component
{
    public User $user;
    public bool $isFollowing = false;

    public function mount(User $user): void
    {
        $this->user = $user;

        if (Auth::check()) {
            $this->isFollowing = Auth::user()
                ->followings()
                ->where('following_id', $user->id)
                ->exists();
        }
    }

    public function toggleFollow(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('register'));
            return;
        }

        if ($this->isFollowing) {
            Auth::user()->followings()->detach($this->user->id);
            $this->isFollowing = false;
        } else {
            Auth::user()->followings()->attach($this->user->id);
            $this->isFollowing = true;
        }
    }

    public function render(): View
    {
        return view('livewire.follow-button');
    }
}
