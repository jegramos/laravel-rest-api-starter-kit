<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Throwable;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @dataProvider validCreateUserInputs */
    public function test_it_should_create_a_user($input, $statusCode)
    {
        $response = $this->postJson('/api/v1/users', $input);
        $response->assertStatus($statusCode);
    }

    /** @throws Throwable */
    public function test_it_should_validate_unique_fields()
    {
        $repository = new UserRepository();
        $input = [
            'email' => 'sample_email@email.com',
            'username' => 'username1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];
        $repository->create($input);

        $response = $this->postJson('/api/v1/users', $input);
        $response->assertStatus(422);

        $response = json_decode($response->decodeResponseJson()->json, true);
        foreach ($response['errors'] as $error) {
            $this->assertTrue(in_array($error['field'], ['username', 'email']));
        }
    }

    /**
     * @throws Throwable
     */
    public function test_it_should_update_a_user()
    {
        $repository = new UserRepository();
        $input = [
            'email' => 'sample_email@email.com',
            'username' => 'username1',
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => 'Jeg',
            'last_name' => 'Ramos'
        ];
        $repository->create($input);

        $user = User::first();

        $edits = [
            'email' => 'sample_email_edited@email.com',
            'username' => 'user_edited',
            'first_name' => 'Jeg edited',
            'last_name' => 'Ramos edited',
            'active' => false,
            'middle_name' => 'Bucu edited',
            'mobile_number' => '+639064647291',
            'telephone_number' => '223331',
            'sex' => 'female',
            'birthday' => '1997-01-05',
            'address_line_1' => 'Address Line 1 edited',
            'address_line_2' => 'Address Line 2 edited',
            'address_line_3' => 'Address Line 3 edited',
            'district' => 'District 1 edited',
            'city' => 'City 1 edited',
            'province' => 'Province 1 edited',
            'postal_code' => '211234',
            'country' => 'Norway',
            'profile_picture_url' => 'https://yahoo.com'
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $edits);
        $response->assertStatus(200);

        $response = json_decode($response->decodeResponseJson()->json, true);

        // compare the input edits to the actual response data
        foreach ($edits as $key => $value) {
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

    public function validCreateUserInputs(): array
    {
        $requiredFieldsOnly = [
            'email' => 'sample_email@email.com',
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
            'telephone_number' => '223333',
            'sex' => 'male',
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
}
