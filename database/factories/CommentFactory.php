<?php
/**
 * @author denis.chernonozhkin
 * @Date 02.09.2026 19:25
 */

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence(),
            'parent_id' => null,
        ];
    }
}
