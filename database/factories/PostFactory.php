<?php
/**
 * @author denis.chernonozhkin
 * @Date 02.09.2026 19:23
 */

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraphs(rand(2, 5), true),
            'image' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
