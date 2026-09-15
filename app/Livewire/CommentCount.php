<?php
/**
 * @author denis.chernonozhkin
 * @Date 15.09.2026 12:04
 */

namespace App\Livewire;

use App\Models\Post;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentCount extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    #[On('comment-added')]
    #[On('comment-deleted')]
    public function refreshCount(): void
    {
        // Просто перезагружаем компонент
        $this->post->refresh();
    }

    public function render(): View
    {
        return view('livewire.comment-count', [
            'count' => $this->post->comments()->whereNull('deleted_at')->count(),
        ]);
    }
}
