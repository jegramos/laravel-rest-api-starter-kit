<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Throwable;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $baseUri = self::BASE_API_URI . '/users';

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $user = User::factory()->has(UserProfile::factory())->create();
        Sanctum::actingAs($user);
    }

    /**
     * @dataProvider validCreateUserInputs
     * @note we can't use Eloquent in data providers
     */
    public function test_it_can_create_a_user($input, $statusCode)
    {
        $input['country_id'] = Country::first()->id;
        $input['profile_picture_path'] = fake()->filePath;

        $response = $this->postJson($this->baseUri, $input);
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
            'sex' => 'male',
            'birthday' => '1997-01-04',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'address_line_3' => 'Address Line 3',
            'district' => 'District 1',
            'city' => 'City 1',
            'province' => 'Province 1',
            'postal_code' => '211'
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

        $response = $this->postJson($this->baseUri, $input);
        $response->assertStatus(422);

        $response = $response->decodeResponseJson();
        foreach ($response['errors'] as $error) {
            $this->assertTrue(in_array($error['field'], ['username', 'email']));
        }
    }

    /** @throws Throwable */
    public function test_it_can_update_a_user()
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
            'middle_name' => fake()->lastName,
            'mobile_number' => '+639064647291',
            'telephone_number' => '+63279434285',
            'sex' => fake()->randomElement(['male', 'female']),
            'birthday' => '1997-01-05',
            'address_line_1' => fake()->buildingNumber,
            'address_line_2' => fake()->streetName,
            'address_line_3' => fake()->streetAddress,
            'district' => 'District 1',
            'city' => fake()->city,
            'province' => 'Province 1',
            'postal_code' => fake()->postcode,
            'country_id' => Country::first()->id,
            'profile_picture_path' => fake()->filePath
        ];

        $response = $this->patchJson("$this->baseUri/$user->id", $edits);
        $response->assertStatus(200);

        $response = $response->decodeResponseJson();

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

            // country info is wrapped in `user_profile.country` field
            if ($key === 'country_id') {
                $result = $response['data']['user_profile']['country']['id'];
                $this->assertEquals($value, $result);
                continue;
            }

            // if the profile_picture_path is provided, a profile_picture_url is returned
            if ($key === 'profile_picture_path') {
                $result = $response['data']['user_profile']['profile_picture_url'];
                $this->assertTrue(URL::isValidUrl($result));
                continue;
            }

            // format birthday as ISO for correct checking
            if ($key === 'birthday') {
                $value = Carbon::create($value)->toISOString();
                $this->assertEquals($value, $response['data']['user_profile']['birthday']);
                continue;
            }

            // profile details are wrapped with a `user_profile` field
            if (!in_array($key, ['username', 'email', 'active'])) {
                $result = $response['data']['user_profile'][$key];
                $this->assertEquals($value, $result);
                continue;
            }

            // the rest are credentials
            $this->assertEquals($value, $response['data'][$key]);
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
        $response = $this->patchJson("$this->baseUri/{$users[0]->id}", $user2Info);
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

        $response = $this->patchJson("$this->baseUri/{$user->id}", $input);
        $response->assertStatus(200);
    }

    /** @dataProvider differentUsernames */
    public function test_it_should_only_accept_alphanumeric_and_dot_for_username($input, $expected)
    {
        $response = $this->postJson($this->baseUri, $input);
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
        $result = $this->postJson($this->baseUri, $input);
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

    public function test_it_can_read_a_user()
    {
        /** @var User $user */
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->get("$this->baseUri/{$user->id}");
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    public function test_it_can_delete_a_user()
    {
        /** @var User $user */
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->delete("$this->baseUri/{$user->id}");
        $response->assertStatus(204);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @throws Throwable */
    public function test_it_can_fetch_users()
    {
        User::factory()->count(5)->has(UserProfile::factory())->create();
        $totalUserCount = User::count('id');

        $response = $this->get($this->baseUri);
        $response = $response->decodeResponseJson();

        $this->assertIsArray($response['data']);
        $this->assertEquals($totalUserCount, count($response['data']));
    }

    /** @throws Throwable */
    public function test_it_can_return_length_aware_paginated_results()
    {
        User::factory()->count(15)->has(UserProfile::factory())->create();
        $totalUserCount = User::count('id');

        $limit = 5;
        $response = $this->get("$this->baseUri?limit=$limit");
        $response = $response->decodeResponseJson();

        $this->assertArrayHasKey('pagination', $response);
        $this->assertEquals($totalUserCount, $response['pagination']['total']);
        $this->assertEquals($limit, count($response['data']));
    }

    /** @throws Throwable */
    public function test_it_sets_up_the_active_and_email_verified_at_fields_when_not_provided()
    {
        $onlyRequiredInputs = $this->getRequiredUserInputSample();
        $response = $this->postJson($this->baseUri, $onlyRequiredInputs);
        $response = $response->decodeResponseJson();
        $user = User::find($response['data']['id']);

        $this->assertTrue($user->active);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_is_not_in_payload_then_email_verified_at_should_be_null()
    {
        $input = $this->getRequiredUserInputSample();
        $response = $this->postJson($this->baseUri, $input);
        $response = $response->decodeResponseJson();
        $user = User::find($response['data']['id']);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_field_is_false_then_email_verified_at_field_should_be_null()
    {
        $input = $this->getRequiredUserInputSample();
        $input['email_verified'] = false;
        $response = $this->postJson($this->baseUri, $input);
        $response = $response->decodeResponseJson();
        $user = User::find($response['data']['id']);
        $this->assertNull($user->email_verified_at);
    }

    /** @throws Throwable */
    public function test_if_email_verified_field_is_true_then_email_verified_at_field_should_be_a_valid_date()
    {
        $input = $this->getRequiredUserInputSample();
        $input['email_verified'] = true;
        $response = $this->postJson($this->baseUri, $input);
        $response = $response->decodeResponseJson();
        $user = User::find($response['data']['id']);
        $this->assertTrue(!!strtotime($user->email_verified_at->toDateString()));
    }

    public function test_it_can_upload_profile_picture()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $file = UploadedFile::fake()->image('fake_image.jpg', 500, 500);
        $response = $this->post("$this->baseUri/$user->id/profile-picture", ['photo' => $file]);
        $response->assertStatus(200);

        // clean the bucket
        Storage::disk('s3')->deleteDirectory('images/');
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
