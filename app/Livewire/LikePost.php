<?php
/**
 * @author denis.chernonozhkin
 * @Date 03.09.2026 23:35
 */

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LikePost extends Component
{
    public Post $post;
    public bool $isLiked;
    public int $likesCount;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->likesCount = $post->likes()->count();

        // Проверяем, лайкнул ли текущий пользователь этот пост
        if (Auth::check()) {
            $this->isLiked = $post->likes()->where('user_id', Auth::id())->exists();
        } else {
            $this->isLiked = false;
        }
    }

    public function toggleLike()
    {
        // Если не авторизован — редирект на регистрацию (исправлено!)
        if (!Auth::check()) {
            return redirect()->route('register');
        }

        // Меняем состояние лайка (оптимистично)
        $this->isLiked = !$this->isLiked;
        $this->likesCount += $this->isLiked ? 1 : -1;

        // Сохраняем в БД (в фоне)
        if ($this->isLiked) {
            $this->post->likes()->create(['user_id' => Auth::id()]);
        } else {
            $this->post->likes()->where('user_id', Auth::id())->delete();
        }
    }

    public function render(): View
    {
        return view('livewire.like-post');
    }
}
