<?php
/**
 * @author denis.chernonozhkin
 * @Date 13.09.2026 22:56
 */

namespace App\Livewire;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LikeComment extends Component
{
    public Comment $comment;
    public bool $isLiked;
    public int $likesCount;

    public function mount(Comment $comment): void
    {
        $this->comment = $comment;
        $this->likesCount = $comment->likes()->count();

        if (Auth::check()) {
            $this->isLiked = $comment->likes()->where('user_id', Auth::id())->exists();
        } else {
            $this->isLiked = false;
        }
    }

    public function toggleLike(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('register'));
            return;
        }

        // Меняем состояние лайка
        $this->isLiked = !$this->isLiked;
        $this->likesCount += $this->isLiked ? 1 : -1;

        if ($this->isLiked) {
            try {
                $this->comment->likes()->firstOrCreate(['user_id' => Auth::id()]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Ловим race condition
                $this->isLiked = false;
                $this->likesCount -= 1;
            }
        } else {
            $this->comment->likes()->where('user_id', Auth::id())->delete();
        }
    }

    public function render(): View
    {
        return view('livewire.like-comment');
    }
}
