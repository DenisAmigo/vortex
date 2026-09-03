<?php
/**
 * @author denis.chernonozhkin
 * @Date 03.09.2026 11:57
 */

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class CreatePost extends Component
{
    public string $content = '';

    public function createPost(): void
    {
        // 1. Валидация
        $this->validate([
            'content' => 'required|string|min:1|max:1000',
        ]);

        // 2. Сохраняем пост
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $this->content,
            'image' => null, // пока без картинок
        ]);

        // 3. Очищаем поле
        $this->content = '';

        // 4. Уведомление (опционально)
        session()->flash('message', 'Пост опубликован!');

        // 5. Отправляем событие (для обновления ленты в будущем)
        $this->dispatch('post-created', $post->id);
    }

    public function render(): View
    {
        return view('livewire.create-post');
    }
}
