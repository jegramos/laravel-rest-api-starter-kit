<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail,
            'username' => fake()->unique()->userName,
            'password' => 'Sample_Password_1',
            'active' => fake()->boolean,
            'email_verified_at' => fake()->date,
        ];
    }
}
