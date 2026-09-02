<?php
/**
 * @author denis.chernonozhkin
 * @Date 02.09.2026 19:25
 */

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'likeable_type' => 'App\Models\Post',
            'likeable_id' => 1,
        ];
    }
}
