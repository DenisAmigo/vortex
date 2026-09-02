<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(): View
    {
        // Получаем все посты с пагинацией (по 10 на страницу)
        $posts = Post::with(['user', 'likes', 'comments'])->latest()->paginate(10);

        return view('feed', compact('posts'));
    }
}
