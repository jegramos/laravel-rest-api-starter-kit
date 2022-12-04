<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $baseUri = self::BASE_API_URI . '/users';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        /** @var User $user */
        $this->user = User::factory()->has(UserProfile::factory())->create();
        $this->user->syncRoles('admin');
        Sanctum::actingAs($this->user);
    }

    public function test_only_admins_can_create_a_user()
    {
        $response = $this->postJson($this->baseUri, $this->getRequiredUserInputSample());
        $response->assertStatus(201);

        $this->user->syncRoles('standard_user');
        $response = $this->postJson($this->baseUri, $this->getRequiredUserInputSample());
        $response->assertStatus(403);
    }

    public function test_only_admins_can_update_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->patchJson("$this->baseUri/$user->id", $this->getRequiredUserInputSample());
        $response->assertStatus(200);

        $this->user->syncRoles('standard_user');
        $response = $this->patchJson("$this->baseUri/$user->id", $this->getRequiredUserInputSample());
        $response->assertStatus(403);
    }

    public function test_only_admins_can_get_all_users()
    {
        $response = $this->getJson("$this->baseUri");
        $response->assertStatus(200);

        $this->user->syncRoles('standard_user');
        $response = $this->getJson("$this->baseUri");
        $response->assertStatus(403);
    }

    public function test_only_admins_can_read_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->get("$this->baseUri/$user->id");
        $response->assertStatus(200);

        $this->user->syncRoles('standard_user');
        $response = $this->get("$this->baseUri/$user->id");
        $response->assertStatus(403);
    }

    public function test_only_admins_can_delete_users()
    {
        $user = User::factory()->has(UserProfile::factory())->create();

        $response = $this->delete("$this->baseUri/$user->id");
        $response->assertStatus(204);

        $this->user->syncRoles('standard_user');
        $response = $this->delete("$this->baseUri/$user->id");
        $response->assertStatus(403);
    }

    public function test_only_admins_can_upload_a_profile_picture_of_a_user()
    {
        $user = User::factory()->has(UserProfile::factory())->create();
        $file = UploadedFile::fake()->image('fake_image.jpg', 500, 500);

        $response = $this->post("$this->baseUri/$user->id/profile-picture", ['photo' => $file]);
        $response->assertStatus(200);

        $this->user->syncRoles('standard_user');
        $response = $this->post("$this->baseUri/$user->id/profile-picture", ['photo' => $file]);
        $response->assertStatus(403);

        // clean the bucket
        Storage::disk('s3')->deleteDirectory('images/');
    }
}
