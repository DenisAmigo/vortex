<?php
/**
 * @author denis.chernonozhkin
 * @Date 18.09.2026 20:21
 */

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
    public function deleting(Post $post): void
    {
        // Удаляем лайки
        $post->likes()->delete();

        // Удаляем картинку из хранилища
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
    }
}
