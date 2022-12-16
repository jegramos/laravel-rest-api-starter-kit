<?php

namespace Database\Factories;

use App\Enums\Role;
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
            'active' => true,
            'email_verified_at' => fake()->date,
        ];
    }

    /**
     * Attach a standard user role after creating or making a user model
     *
     * @return UserFactory
     */
    public function configure(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Role::STANDARD_USER->value);
        })->afterMaking(function (User $user) {
            $user->assignRole(Role::STANDARD_USER->value);
        });
    }

    /**
     * @State
     * User is suspended
     *
     * @return Factory
     */
    public function suspended(): Factory
    {
        return $this->state(function () {
            return ['active' => false];
        });
    }

    /**
     * @State
     * User has their email unverified
     *
     * @return Factory
     */
    public function unVerified(): Factory
    {
        return $this->state(function () {
            return ['email_verified_at' => null];
        });
    }
}
