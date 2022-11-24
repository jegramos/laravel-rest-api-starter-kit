<?php

namespace Tests\Feature;

use App\Repositories\Eloquent\UserRepository;
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
        $input = $this->getRequiredUserInputSample();
        $this->createUser($input);

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
        $input = $this->getRequiredUserInputSample();
        $user = $this->createUser($input);

        $edits = [
            'email' => $this->faker->unique()->safeEmail,
            'username' => 'username_edited',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'password' => 'Sample123_123',
            'password_confirmation' => 'Sample123_123',
            'active' => false,
            'middle_name' => $this->faker->lastName,
            'mobile_number' => '+639064647291',
            'telephone_number' => '+63279434285',
            'sex' => 'female',
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

        $response = $this->patchJson("/api/v1/users/{$user['id']}", $edits);
        $response->assertStatus(200);

        $response = json_decode($response->decodeResponseJson()->json, true);

        // compare the input edits to the actual response data
        foreach ($edits as $key => $value) {
            // ignore hidden fields in the response
            if (in_array($key, ['password', 'password_confirmation'])) {
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
    public function test_it_should_validate_unique_fields_when_updating_a_user()
    {
        $input = $this->getRequiredUserInputSample();
        $user1 = $this->createUser($input);

        $input2 = [
            'email' => $this->faker->unique()->safeEmail,
            'username' => 'second_username_2',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName
        ];
        $this->createUser($input2);

        $response = $this->patchJson("/api/v1/users/{$user1['id']}", $input2);
        $response->assertStatus(422);
    }

    /** @throws Throwable */
    public function test_it_should_ignore_unique_validation_when_updating_the_same_user_with_the_same_field_values()
    {
        $input = $this->getRequiredUserInputSample();
        $user = $this->createUser($input);

        $response = $this->patchJson("/api/v1/users/{$user['id']}", $input);
        $response->assertStatus(200);
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
        $input = $this->getRequiredUserInputSample();
        $user = $this->createUser($input);

        $response = $this->delete("api/v1/users/{$user['id']}");
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    private function createUser(array $input): array
    {
        $repository = new UserRepository();
        return $repository->create($input);
    }

    /**
     * Generate required user info input
     *
     * @return array
     */
    private function getRequiredUserInputSample(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'username' => 'username_1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName
        ];
    }
}
