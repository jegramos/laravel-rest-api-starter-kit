<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'middle_name' => fake()->lastName,
            'mobile_number' => '+63906' . fake()->randomNumber(7),
            'telephone_number' => '+6327' .fake()->randomNumber(7),
            'sex' => fake()->randomElement(['male', 'female']),
            'birthday' => fake()->date('Y-m-d'),
            'address_line_1' => fake()->streetName,
            'address_line_2' => fake()->streetAddress,
            'address_line_3' => fake()->streetAddress,
            'district' => 'District ' . fake()->city,
            'city' => fake()->city,
            'province' => 'Province ' . fake()->city,
            'postal_code' => fake()->postcode,
            'country_id' => Country::first()->id,
            'profile_picture_path' => null /** @note is null to avoid unnecessary S3 calls */
        ];
    }
}
