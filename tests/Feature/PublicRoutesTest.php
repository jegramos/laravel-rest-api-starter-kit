<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @throws Throwable */
    public function test_user_can_check_for_username_availability()
    {
        $email = strtoupper(fake()->email);
        User::factory()->has(UserProfile::factory())->create(['email' => $email]);

        $response = $this->get(self::BASE_API_URI.'/availability/email?value='.strtoupper($email));
        $response->assertStatus(200);

        $response = $response->decodeResponseJson();
        $this->assertFalse($response['data']['is_available']);
    }

    /** @throws Throwable */
    public function test_user_can_check_for_email_availability()
    {
        $username = strtoupper(fake()->userName);
        User::factory()->has(UserProfile::factory())->create(['username' => $username]);

        $response = $this->get(self::BASE_API_URI.'/availability/username?value='.strtolower($username));
        $response->assertStatus(200);

        $response = $response->decodeResponseJson();
        $this->assertFalse($response['data']['is_available']);
    }

    /** @throws Throwable */
    public function test_user_can_retrieve_a_list_of_countries()
    {
        $response = $this->get(self::BASE_API_URI.'/countries');
        $response->assertStatus(200);

        $response = $response->decodeResponseJson();
        $this->assertTrue(count($response['data']) > 0);
    }
}
