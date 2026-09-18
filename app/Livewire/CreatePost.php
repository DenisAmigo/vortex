<?php
/**
 * @author denis.chernonozhkin
 * @Date 03.09.2026 11:57
 */

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{
    use WithFileUploads;

    public string $content = '';
    public $image;

    protected array $rules = [
        'content' => 'required|string|max:1000',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ];

    public function createPost(): void
    {
        // 1. Валидация
        $this->validate();

        // 2. Обработка медиа
        $imagePath = null;

        if ($this->image) {
            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }

            $extension = $this->image->getClientOriginalExtension();
            $filename = uniqid('post_') . '.' . $extension;
            $path = 'posts/' . $filename;

            // Сжимаем до 1500px по большей стороне
            $manager = new ImageManager(new Driver());
            $manager->read($this->image->getRealPath())
                ->scaleDown(width: 1500)
                ->save(storage_path('app/public/' . $path));

            $imagePath = $path;
        }

        // 2. Сохраняем пост
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $this->content,
            'image' => $imagePath,
        ]);

        // 3. Очищаем поле
        $this->reset('content', 'image');

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
