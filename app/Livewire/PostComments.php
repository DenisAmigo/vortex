<?php
/**
 * @author denis.chernonozhkin
 * @Date 14.09.2026 0:29
 */

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    #[On('comment-added')]
    public function refreshComments(): void
    {
        // Просто перезагружаем компонент, чтобы подтянуть новые комментарии
        $this->post->refresh();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        // Проверяем, что пользователь - автор комментария
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Вы не можете удалить этот комментарий!');
        }

        $comment->delete();

        // Обновляем список комментариев
        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        return view('livewire.post-comments', [
            'comments' => $this->post->comments()
                ->with('user', 'likes')
                ->whereNull('deleted_at')
                ->latest()
                ->get(),
        ]);
    }
}
