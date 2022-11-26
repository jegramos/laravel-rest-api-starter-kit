<?php

namespace Tests\Unit;

use App\Enums\PaginationType;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use Throwable;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository(new User());
    }

    /** @throws Throwable */
    public function test_it_can_create_a_user()
    {
        $this->userRepository->create($this->getUserDetails());
        $this->assertDatabaseCount('users', 1);
    }

    /** @throws Throwable */
    public function test_it_can_delete_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $deletedUser = $this->userRepository->destroy($user->id);
        $this->assertEquals($user->id, $deletedUser['id']);
    }

    public function test_it_can_read_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $foundUser = $this->userRepository->read($user->id);
        $this->assertEquals($user->id, $foundUser['id']);
    }

    /** @throws Throwable */
    public function test_it_can_update_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $edited = ['first_name' => fake()->firstName, 'username' => fake()->unique()->userName];
        $editedUser = $this->userRepository->update($user->id, $edited);

        $this->assertEquals($edited['first_name'], $editedUser['user_profile']['first_name']);
        $this->assertEquals($edited['username'], $editedUser['username']);
    }

    public function test_it_can_fetch_all_users()
    {
        $count = 25;
        User::factory()->has(UserProfile::factory())->count($count)->create();
        $users = $this->userRepository->all();
        $this->assertEquals($count, count($users));
    }

    public function test_it_can_fetch_all_users_with_pagination()
    {
        $count = 30;
        User::factory()->has(UserProfile::factory())->count($count)->create();

        $request = new Request();
        $limit = 10;
        $request->replace(['limit' => $limit]);
        app()->instance('request', $request);

        $users = $this->userRepository->all(PaginationType::LENGTH_AWARE);

        $this->assertEquals($count, $users['total']);
        $this->assertEquals($limit, count($users['data']));
    }

    /**
     * Generate user info
     */
    private function getUserDetails(): array
    {
        return [
            'email' => fake()->unique()->safeEmail,
            'username' => fake()->unique()->userName,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'password' => 'Sample123_123',
            'password_confirmation' => 'Sample123_123',
            'active' => fake()->boolean,
            'email_verified' => fake()->boolean,
            'middle_name' => $this->faker->lastName,
            'mobile_number' => '+639064647291',
            'telephone_number' => '+63279434285',
            'sex' => fake()->randomElement(['male', 'female']),
            'birthday' => '1997-01-05',
            'address_line_1' => $this->faker->buildingNumber,
            'address_line_2' => $this->faker->streetName,
            'address_line_3' => $this->faker->streetAddress,
            'district' => 'District 1',
            'city' => $this->faker->city,
            'province' => 'Province 1',
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'profile_picture_url' => $this->faker->imageUrl
        ];
    }
}
