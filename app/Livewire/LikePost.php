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
    public $likers = [];
    public int $totalLikersCount;

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

    public function loadLikers()
    {
        $likers = $this->post->likes()
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->values();

        // Если пользователь авторизован, сортируем
        if (Auth::check()) {
            $currentUser = Auth::user();
            $followingsIds = $currentUser->followings()->pluck('users.id')->toArray();

            $likers = $likers->sortByDesc(function ($user) use ($currentUser, $followingsIds) {
                // Сначала текущий пользователь
                if ($user->id === $currentUser->id) {
                    return 3;
                }
                // Потом подписчики
                if (in_array($user->id, $followingsIds)) {
                    return 2;
                }
                // Потом все остальные
                return 1;
            })->values();
        }

        $this->likers = $likers->take(3);

        $this->totalLikersCount = $likers->count();
    }

    public function render(): View
    {
        return view('livewire.like-post');
    }
}
