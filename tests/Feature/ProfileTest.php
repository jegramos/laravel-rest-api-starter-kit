<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $this->user = User::factory()->has(UserProfile::factory())->create();
        $this->user->syncRoles('super_user');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_view_profile()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
