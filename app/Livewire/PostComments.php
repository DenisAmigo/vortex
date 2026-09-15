<?php
/**
 * @author denis.chernonozhkin
 * @Date 14.09.2026 0:29
 */

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;
    public ?int $editingCommentId = null;
    public string $editingContent = '';
    public ?int $replyingToCommentId = null;
    public string $replyContent = '';

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

    /**
     * Начало функционала редактирования комментария
     *
     * @param int $commentId
     * @return void
     */
    public function startEditing(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            throw new AuthorizationException('Вы не можете редактировать этот комментарий!');
        }

        $this->editingCommentId = $commentId;
        $this->editingContent = $comment->content;
    }

    /**
     * Сохранение отредактированного комментария
     *
     * @return void
     */
    public function saveEdit(): void
    {
        $this->validate([
            'editingContent' => 'required|string|max:1000',
        ]);

        $comment = Comment::findOrFail($this->editingCommentId);

        if ($comment->user_id !== auth()->id()) {
            throw new AuthorizationException('Вы не можете редактировать этот комментарий!');
        }

        $comment->update([
            'content' => $this->editingContent,
        ]);

        $this->editingCommentId = null;
        $this->editingContent = '';

        $this->dispatch('comment-updated');
    }

    /**
     * Отмена редактирования
     *
     * @return void
     */
    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    /**
     * Начало функционала ответа на комментарий
     *
     * @param int $commentId
     * @return void
     */
    public function startReply(int $commentId): void
    {
        if (!auth()->check()) {
            $this->redirect(route('register'));
            return;
        }

        $this->replyingToCommentId = $commentId;
        $this->replyContent = '';
        // Закрываем редактирование, если оно открыто
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    /**
     * Отмена ответа
     *
     * @return void
     */
    public function cancelReply(): void
    {
        $this->replyingToCommentId = null;
        $this->replyContent = '';
    }

    /**
     * Сохранение ответа на комментарий
     *
     * @return void
     */
    public function addReply(): void
    {
        $this->validate([
            'replyContent' => 'required|string|max:1000',
        ]);

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->replyContent,
            'parent_id' => $this->replyingToCommentId,
        ]);

        $this->replyingToCommentId = null;
        $this->replyContent = '';

        $this->dispatch('comment-added');
        $this->dispatch('$refresh');
    }

    public function addComment(): void
    {
        $this->validate([
            'editingContent' => 'required|string|max:1000',
        ]);

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->editingContent,
            'parent_id' => null,
        ]);

        $this->editingContent = '';

        $this->dispatch('comment-added');
        $this->dispatch('$refresh');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        // Проверяем, что пользователь - автор комментария
        if ($comment->user_id !== auth()->id()) {
            throw new AuthorizationException('Вы не можете удалить этот комментарий!');
        }

        $comment->delete();

        $this->dispatch('comment-deleted');
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
