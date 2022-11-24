<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Throwable;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @dataProvider validCreateUserInputs */
    public function test_it_can_create_a_user($input, $statusCode)
    {
        $response = $this->postJson('/api/v1/users', $input);
        $response->assertStatus($statusCode);
    }

    public function validCreateUserInputs(): array
    {
        $requiredFieldsOnly = [
            'email' => 'sample@email.com',
            'username' => 'username1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];

        $allFields = array_merge($requiredFieldsOnly, [
            'active' => true,
            'email_verified' => false,
            'middle_name' => 'Bucu',
            'mobile_number' => '+639064647295',
            'telephone_number' => '+63279434285',
            'sex' =>'male',
            'birthday' => '1997-01-04',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'address_line_3' => 'Address Line 3',
            'district' => 'District 1',
            'city' => 'City 1',
            'province' => 'Province 1',
            'postal_code' => '211',
            'country' => 'Philippines',
            'profile_picture_url' => 'https://google.com'
        ]);

        $missingRequiredFields = Arr::except(
            $allFields,
            ['username', 'email', 'password', 'password_confirmation', 'first_name', 'last_name']
        );

        return [
            [$requiredFieldsOnly, 201],
            [$allFields, 201],
            [$missingRequiredFields, 422]
        ];
    }

    /** @throws Throwable */
    public function test_it_should_validate_unique_fields_when_creating_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $input = $this->getRequiredUserInputSample();
        $input['email'] = $user->email;
        $input['username'] = $user->username;

        $response = $this->postJson('/api/v1/users', $input);
        $response->assertStatus(422);

        $response = json_decode($response->decodeResponseJson()->json, true);
        foreach ($response['errors'] as $error) {
            $this->assertTrue(in_array($error['field'], ['username', 'email']));
        }
    }

    /** @throws Throwable */
    public function test_it_should_update_a_user()
    {
        $user = User::factory()
            ->has(UserProfile::factory())
            ->create();

        $edits = [
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

        $response = $this->patchJson("/api/v1/users/{$user->id}", $edits);
        $response->assertStatus(200);

        $response = json_decode($response->decodeResponseJson()->json, true);

        // compare the input edits to the actual response data
        foreach ($edits as $key => $value) {
            // ignore hidden fields in the response
            if (in_array($key, ['password', 'password_confirmation'])) {
                continue;
            }

            // when email_verified is set to `true`, response will have
            // an email_verified_at key with a timestamp value -- and it will be set to null if it's `false`
            if ($key === 'email_verified') {
                $this->assertEquals($value, !!strtotime($response['data']['email_verified_at']));
                continue;
            }

            // profile details are wrapped with a `user_profile` field
            $actual = in_array($key, ['username', 'email', 'active'])
                ? $response['data'][$key]
                : $response['data']['user_profile'][$key];

            // format birthday as ISO for correct checking
            if ($key === 'birthday') {
                $value = Carbon::create($value)->toISOString();
            }

            $this->assertEquals($value, $actual);
        }
    }

    /** @throws Throwable */
    public function test_it_should_validate_unique_username_and_email_when_updating_a_user()
    {
        $users = User::factory()->count(2)->has(UserProfile::factory())->create();
        $user2Info = [
            'email' => $users[1]->email,
            'username' => $users[1]->username,
        ];

        // try to update the first user's username and email with user 2's
        $response = $this->patchJson("/api/v1/users/{$users[0]->id}", $user2Info);
        $response->assertStatus(422);
    }

    /** @throws Throwable */
    public function test_it_should_ignore_unique_validation_when_updating_the_same_user_with_the_same_field_values()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $input = [
            'email' => $user->email,
            'username' => $user->username
        ];

        $response = $this->patchJson("/api/v1/users/{$user->id}", $input);
        $response->assertStatus(200);
    }

    /** @dataProvider differentUsernames */
    public function test_it_should_only_accept_alphanumeric_and_dot_for_username($input, $expected)
    {
        $response = $this->postJson('api/v1/users', $input);
        $response->assertStatus($expected);
    }

    public function differentUsernames(): array
    {
        $requiredFields = [
            'email' => 'sample_email@email.com',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];

        return [
            [array_merge($requiredFields, ['username' => 'jegramos']), 201],
            [array_merge($requiredFields, ['username' => 'jeg.ramos']), 201],
            [array_merge($requiredFields, ['username' => 'jeg-ramos.04']), 201],
            [array_merge($requiredFields, ['username' => 'jegramos-ramos-04']), 201],
            [array_merge($requiredFields, ['username' => 'jegramos_ramos-04']), 201],
            [array_merge($requiredFields, ['username' => 'jegramos-ramos.04']), 201],
            [array_merge($requiredFields, ['username' => 'jeg ramos']), 422],
            [array_merge($requiredFields, ['username' => 'jegramos!']), 422],
            [array_merge($requiredFields, ['username' => 'jegramos :)']), 422],
            [array_merge($requiredFields, ['username' => 'jeg+ramos']), 422],
        ];
    }

    /** @dataProvider differentMobileNumbers */
    public function test_it_should_validate_mobile_number_formats($input, $statusCode)
    {
        $result = $this->postJson('api/v1/users', $input);
        $result->assertStatus($statusCode);
    }

    public function differentMobileNumbers(): array
    {
        $requiredFields = [
            'email' => 'sample_email@email.com',
            'username' => 'username1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];

        return [
            [array_merge($requiredFields, ['mobile_number' => '+639064647295']), 201],
            [array_merge($requiredFields, ['mobile_number' => '+63 9064647295']), 422],
            [array_merge($requiredFields, ['mobile_number' => '639064647295']), 422],
            [array_merge($requiredFields, ['mobile_number' => '09064647295']), 422],
        ];
    }

    /** @dataProvider differentTelephoneNumbers */
    public function test_it_should_validate_telephone_number_formats($input, $statusCode)
    {
        $result = $this->postJson('api/v1/users', $input);
        $result->assertStatus($statusCode);
    }

    public function differentTelephoneNumbers(): array
    {
        $requiredFields = [
            'email' => 'sample_email@email.com',
            'username' => 'username1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];

        return [
            [array_merge($requiredFields, ['telephone_number' => '+63279434285']), 201],
            [array_merge($requiredFields, ['telephone_number' => '+63 279434285']), 422],
            [array_merge($requiredFields, ['telephone_number' => '63279434285']), 422],
            [array_merge($requiredFields, ['telephone_number' => '279434285']), 422],
        ];
    }

    /** @throws Throwable */
    public function test_it_can_delete_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->delete("api/v1/users/{$user->id}");
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    public function test_it_can_fetch_users()
    {
        $usersCount = 7;
        User::factory()->count($usersCount)->has(UserProfile::factory())->create();

        $response = $this->get('api/v1/users');
        $response = json_decode($response->decodeResponseJson()->json, true);

        $this->assertIsArray($response['data']);
        $this->assertEquals($usersCount, count($response['data']));
    }

    /** @throws Throwable */
    public function test_it_can_return_length_aware_paginated_results()
    {
        $usersCount = 15;
        User::factory()->count($usersCount)->has(UserProfile::factory())->create();

        $limit = 5;
        $response = $this->get("api/v1/users?limit=$limit");
        $response = json_decode($response->decodeResponseJson()->json, true);

        $this->assertArrayHasKey('pagination', $response);
        $this->assertEquals($usersCount, $response['pagination']['total']);
        $this->assertEquals($limit, count($response['data']));
    }

    /** @throws Throwable */
    public function test_it_sets_up_the_active_and_email_verified_at_fields_when_not_provided()
    {
        $onlyRequiredInputs = $this->getRequiredUserInputSample();
        $response = $this->postJson('api/v1/users', $onlyRequiredInputs);
        $response = json_decode($response->decodeResponseJson()->json, true);
        $user = User::find($response['data']['id']);

        $this->assertTrue($user->active);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_at_is_null_if_email_verified_field_not_in_payload()
    {
        $input = $this->getRequiredUserInputSample();
        $response = $this->postJson('api/v1/users', $input);
        $response = json_decode($response->decodeResponseJson()->json, true);
        $user = User::find($response['data']['id']);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_at_is_null_if_email_verified_field_is_false()
    {
        $input = $this->getRequiredUserInputSample();
        $input['email_verified'] = false;
        $response = $this->postJson('api/v1/users', $input);
        $response = json_decode($response->decodeResponseJson()->json, true);
        $user = User::find($response['data']['id']);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_at_is_a_valid_date_if_email_verified_field_is_true()
    {
        $input = $this->getRequiredUserInputSample();
        $input['email_verified'] = true;
        $response = $this->postJson('api/v1/users', $input);
        $response = json_decode($response->decodeResponseJson()->json, true);
        $user = User::find($response['data']['id']);
        $this->assertTrue(!!strtotime($user->email_verified_at->toDateString()));
    }

    /**
     * Generate required user info input
     *
     * @return array
     */
    private function getRequiredUserInputSample(): array
    {
        return [
            'email' => fake()->unique()->safeEmail,
            'username' => fake()->unique()->userName,
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName
        ];
    }
}
