<?php
/**
 * @author denis.chernonozhkin
 * @Date 15.09.2026 17:38
 */

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class AvatarUpload extends Component
{
    use WithFileUploads;

    public User $user;
    public $photo;
    public bool $showViewModal = false;
    public bool $showDeleteConfirm = false;

    protected array $rules = [
        'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
    ];

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function updatedPhoto(): void
    {
        $this->validate();
        $this->upload();
    }

    public function upload(): void
    {
        $user = Auth::user();

        if (!$this->photo) {
            return;
        }

        // Удаляем старую папку пользователя (если есть)
        Storage::disk('public')->deleteDirectory('avatars/' . $user->id);

        // Создаём папку для пользователя
        $directory = 'avatars/' . $user->id;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $extension = $this->photo->getClientOriginalExtension();
        $originalPath = 'avatars/' . $user->id . '/original.' . $extension;
        $thumbPath = 'avatars/' . $user->id . '/thumb.' . $extension;

        // Сохраняем оригинал (сжимаем до 1500px по большей стороне)
        $manager = new ImageManager(new Driver());
        $image = $manager->read($this->photo->getRealPath());

        // Оригинал: сжимаем, сохраняя пропорции, если больше 1500px
        $image->scaleDown(width: 1500)->save(storage_path('app/public/' . $originalPath));

        // Thumb: fit 150x150 (умная обрезка)
        $manager->read($this->photo->getRealPath())
            ->cover(150, 150)
            ->save(storage_path('app/public/' . $thumbPath));

        // Обновляем БД
        $user->update(['avatar' => $thumbPath]);
        $user->touch();

        $this->user->refresh();
        $this->reset('photo');
        $this->showViewModal = false;

        session()->flash('avatar-message', 'Аватар обновлён!');
    }

    public function deleteAvatar(): void
    {
        $user = Auth::user();

        Storage::disk('public')->deleteDirectory('avatars/' . $user->id);
        $user->update(['avatar' => null]);
        $this->user->refresh();

        $this->showDeleteConfirm = false;
        $this->showViewModal = false;

        session()->flash('avatar-message', 'Аватар удалён.');
    }

    public function render(): View
    {
        return view('livewire.avatar-upload');
    }
}
