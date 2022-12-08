<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\SexualCategory;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Throwable;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $baseUri = self::BASE_API_URI . '/profile';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $this->user = User::factory()->has(UserProfile::factory())->create();
        $this->user->syncRoles(Role::STANDARD_USER->value);
        Sanctum::actingAs($this->user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_view_profile(): void
    {
        $response = $this->get($this->baseUri);
        $response->assertStatus(200);
    }

    /**
     * @throws Throwable
     */
    public function test_user_can_update_profile(): void
    {
        $edits = [
            'username' => fake()->unique()->userName,
            'email' => fake()->unique()->email,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'middle_name' => fake()->lastName,
            'sex' => fake()->randomElement([SexualCategory::MALE->value, SexualCategory::FEMALE->value]),
            'telephone_number' => '+63279434211',
            'mobile_number' => '+639064647210',
            'birthday' => '1997-01-04',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'address_line_3' => 'Address Line 3',
            'district' => 'District 1',
            'city' => 'City 1',
            'province' => 'Province 1',
            'postal_code' => '221'
        ];

        $response = $this->patchJson($this->baseUri, $edits);
        $response->assertStatus(200);
        $result = $response->decodeResponseJson();

        foreach ($edits as $key => $value) {
            // check for credentials correctness
            if (in_array($key, ['username', 'email'])) {
                $this->assertEquals($value, $result['data'][$key]);
                continue;
            }

            // country info is wrapped in `user_profile.country` field
            if ($key === 'country_id') {
                $result = $response['data']['user_profile']['country']['id'];
                $this->assertEquals($value, $result);
                continue;
            }

            // format birthday as ISO for correct checking
            if ($key === 'birthday') {
                $value = Carbon::create($value)->toISOString();
                $this->assertEquals($value, $response['data']['user_profile']['birthday']);
                continue;
            }

            // profile details are wrapped with a `user_profile` field
            $result = $response['data']['user_profile'][$key];
            $this->assertEquals($value, $result);
        }
    }

    public function test_it_can_upload_profile_picture()
    {
        $file = UploadedFile::fake()->image('fake_image.jpg', 500, 500);
        $response = $this->post("$this->baseUri/profile-picture", ['photo' => $file]);
        $response->assertStatus(200);

        // clean the bucket
        Storage::disk('s3')->deleteDirectory('images/');
    }

    public function test_user_can_change_password()
    {
        $oldPassword = 'OldPassword123';
        $this->user->password = $oldPassword;
        $this->user->save();

        $newPassword = 'NewPassword123';
        $input = [
            'old_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ];
        $result = $this->patchJson("$this->baseUri/password", $input);
        $result->assertStatus(200);

        // login again with the new password
        $creds = ['email' => $this->user->email, 'password' => $newPassword];
        $response = $this->post("api/v1/auth/tokens", $creds);
        $response->assertStatus(200);
    }
}
