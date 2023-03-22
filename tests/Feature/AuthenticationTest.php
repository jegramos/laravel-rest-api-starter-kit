<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\Auth\QueuedResetPasswordNotification;
use App\Notifications\Auth\QueuedVerifyEmailNotification;
use App\Notifications\WelcomeNotification;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Throwable;

class AuthenticationTest extends TestCase
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

        Notification::fake();

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

    /**
     * @throws Throwable
     */
    public function test_it_can_register_a_user()
    {
        $input = [
            'email' => fake()->unique()->email,
            'username' => fake()->unique()->userName,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'password' => 'SamplePass123',
            'password_confirmation' => 'SamplePass123'
        ];

        $response = $this->postJson("$this->baseUri/register", $input);
        $response->assertStatus(201);

        $createdUser = User::find($response->decodeResponseJson()['data']['user']['id']);
        Notification::assertSentTo($createdUser, WelcomeNotification::class);
        Notification::assertSentTo($createdUser, QueuedVerifyEmailNotification::class);
    }

    /** @throws Throwable */
    public function test_a_user_created_via_registration_is_always_a_standard_user()
    {
        $input = [
            'email' => fake()->unique()->email,
            'username' => fake()->unique()->userName,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'password' => 'SamplePass123',
            'password_confirmation' => 'SamplePass123'
        ];

        $response = $this->postJson("$this->baseUri/register", $input);
        $roles = $response->decodeResponseJson()['data']['user']['roles'];
        $this->assertEquals(1, count($roles));
        $this->assertEquals(Role::STANDARD_USER->value, $roles[0]['name']);
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

    /**
     * @throws Exception
     */
    public function test_users_can_request_a_password_reset_email()
    {
        Notification::fake();

        $response = $this->post("$this->baseUri/forgot-password", ['email' => $this->user->email]);
        $response->assertStatus(200);

        Notification::assertSentTo($this->user, QueuedResetPasswordNotification::class);
    }

    public function test_users_can_reset_their_passwords()
    {
        $token = app('auth.password.broker')->createToken($this->user);
        $newPassword = 'Sample123123';
        $input = [
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ];

        $response = $this->postJson("$this->baseUri/reset-password", $input);
        $response->assertStatus(200);

        // login again
        $creds = ['email' => $this->user->email, 'password' => $newPassword];
        $response = $this->post("$this->baseUri/tokens", $creds);
        $response->assertStatus(200);
    }
}
