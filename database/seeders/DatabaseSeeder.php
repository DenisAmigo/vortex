<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем таблицы (чтобы не было дублей при повторном запуске)
        Post::query()->truncate();
        Comment::query()->truncate();
        Like::query()->truncate();
        DB::table('subscriptions')->truncate();

        // 1. Создаём 10 пользователей, если их нет
        $existingCount = User::count();
        $targetCount = 10;

        if ($existingCount < $targetCount) {
            // Создаём недостающих пользователей (без удаления существующих)
            User::factory($targetCount - $existingCount)->create();
        }

        $users = User::all();

        // 2. Создаём посты (по 3 от каждого пользователя)
        $posts = collect();
        $users->each(function ($user) use ($posts) {
            for ($i = 0; $i < 3; $i++) {
                $posts->push(Post::factory()->create(['user_id' => $user->id]));
            }
        });

        // 3. Создаём комментарии (по 5 на каждый пост, случайные пользователи)
        $posts->each(function ($post) use ($users) {
            for ($i = 0; $i < 5; $i++) {
                Comment::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        });

        // 4. Создаём лайки (каждый пользователь ставит лайк на 3 случайных поста)
        $users->each(function ($user) use ($posts) {
            $randomPosts = $posts->random(3);
            $randomPosts->each(function ($post) use ($user) {
                Like::factory()->create([
                    'user_id' => $user->id,
                    'likeable_type' => Post::class,
                    'likeable_id' => $post->id,
                ]);
            });
        });

        // 5. Создаём подписки (случайные, но без дублей)
        $users->each(function ($user) use ($users) {
            $followings = $users->where('id', '!=', $user->id)->random(3);
            $followings->each(function ($following) use ($user) {
                DB::table('subscriptions')->insert([
                    'follower_id' => $user->id,
                    'following_id' => $following->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        });
    }
}
