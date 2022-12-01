<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Throwable;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $baseUri = self::BASE_API_URI . '/auth';
    private array $userCreds;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $this->userCreds = [
            'email' => 'jegramos-test@sample.com',
            'password' => 'Jeg123123!'
        ];

        $this->user = User::factory($this->userCreds)->has(UserProfile::factory())->create();
    }

    /** @throws Throwable */
    public function test_user_can_request_an_access_token()
    {
        $response = $this->post("$this->baseUri/tokens", [
            'email' => $this->userCreds['email'],
            'password' => $this->userCreds['password'],
        ]);

        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('token', $result['data']);
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    public function test_user_can_request_access_token_with_user_info()
    {
        $response = $this->post("$this->baseUri/tokens", [
            'email' => $this->userCreds['email'],
            'password' => $this->userCreds['password'],
            'with_user' => true
        ]);

        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('user', $result['data']);
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    public function test_user_can_request_access_token_with_client_name()
    {
        $clientName = "Jeg's Chrome Browser";
        $response = $this->post("$this->baseUri/tokens", [
            'email' => $this->userCreds['email'],
            'password' => $this->userCreds['password'],
            'client_name' => $clientName
        ]);

        $result = $response->decodeResponseJson();
        $this->assertEquals($clientName, $result['data']['token_name']);
        $response->assertStatus(200);
    }

    /** @throws Throwable */
    public function test_user_can_fetch_all_access_tokens_owned()
    {
        // create token with browser
        $this->post("$this->baseUri/tokens", array_merge($this->userCreds, ['client_name' => 'Chrome']));

        // create token with phone
        $this->post("$this->baseUri/tokens", array_merge($this->userCreds, ['client_name' => 'My iPhone14']));

        Sanctum::actingAs($this->user);
        $response = $this->get("$this->baseUri/tokens", $this->userCreds);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /** @throws Throwable */
    public function test_user_must_be_logged_in_to_fetch_tokens()
    {
        $response = $this->get("$this->baseUri/tokens", $this->userCreds);
        $response->assertStatus(401);
    }

    public function test_user_can_revoke_current_access_token()
    {
        $user = Sanctum::actingAs($this->user);
        $response = $this->delete("$this->baseUri/tokens");

        $response->assertStatus(204);
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_user_can_revoke_specific_access_tokens()
    {
        $this->post("$this->baseUri/tokens", array_merge($this->userCreds, ['client_name' => 'Chrome']));

        $user = Sanctum::actingAs($this->user);
        $tokenId = $user->tokens()->first()->id;
        $response = $this->post("$this->baseUri/tokens/revoke", ['token_ids' => [$tokenId]]);

        $response->assertStatus(204);
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_user_can_revoke_all_access_tokens()
    {
        // create multiple tokens
        $this->post("$this->baseUri/tokens", array_merge($this->userCreds, ['client_name' => 'Chrome']));
        $this->post("$this->baseUri/tokens", array_merge($this->userCreds, ['client_name' => 'My iPhone14']));

        $user = Sanctum::actingAs($this->user);
        $response = $this->post("$this->baseUri/tokens/revoke", ['token_ids' => ['*']]);
        $response->assertStatus(204);
        $this->assertEquals(0, $user->tokens()->count());
    }
}
