<?php
/**
 * @author denis.chernonozhkin
 * @Date 13.09.2026 23:32
 */

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddComment extends Component
{
    public Post $post;
    public string $content = '';

    protected array $rules = [
        'content' => 'required|string|max:1000',
    ];

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function addComment(): void
    {
        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => Auth::id(),
            'content' => $this->content,
            'parent_id' => null,
        ]);

        // Очищаем поле
        $this->content = '';

        // Отправляем событие для обновления списка комментариев
        $this->dispatch('comment-added');
    }

    public function render(): View
    {
        return view('livewire.add-comment');
    }
}
