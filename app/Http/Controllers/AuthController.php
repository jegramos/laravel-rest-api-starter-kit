<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Password;
use Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiController
{
    /**
     * Grant the user an access token
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function store(AuthRequest $request): JsonResponse
    {
        $user = User::with('userProfile')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            return $this->error(
                'Invalid username or password',
                Response::HTTP_UNAUTHORIZED,
                ApiErrorCode::INVALID_CREDENTIALS
            );
        }

        // For the token name, clients can optionally send 'My iPhone14', 'Google Chrome', etc.
        $tokenName = $request->get('client_name') ?? 'api_token';

        /**
         * We'll set the abilities to allow everything [*]. Authorization will be handled by Spatie
         * @see https://spatie.be/docs/laravel-permission/v5/introduction
         */
        $expiresAt = now()->addHours(12);
        $token = $user->createToken($tokenName, ['*'], $expiresAt)->plainTextToken;
        $data = ['token' => $token, 'token_name' => $tokenName, 'expires_at' => $expiresAt];

        if ($request->get('with_user')) {
            $data['user'] = $user;
        }

        return $this->success(['data' => $data], Response::HTTP_OK);
    }

    /**
     * Revoke the current access token of the user
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Retrieve all the access tokens of a user
     *
     * @return JsonResponse
     */
    public function fetch(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $tokens = $user->tokens
            ->map(function (PersonalAccessToken $token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'expires_at' => $token->expires_at,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at
                ];
            })
            // only get un-expired tokens
            ->reject(fn (array $token) => now() >= $token['expires_at'])
            ->values();

        return $this->success(['data' => $tokens->toArray()], Response::HTTP_OK);
    }

    /**
     * Revoke specified access tokens owned by the user
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function revoke(AuthRequest $request): JsonResponse
    {
        $tokensToRevoke = $request->get('token_ids');

        // delete everything if they pass a star (*)
        if ($tokensToRevoke === ['*']) {
            auth()->user()->tokens()->delete();
            return $this->success(null, Response::HTTP_NO_CONTENT);
        }

        foreach ($tokensToRevoke as $tokenId) {
            auth()->user()->tokens()->where('id', $tokenId)->delete();
        }

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Verify Email
     *
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();
        return $this->success(['message' => 'Email successfully verified'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Resend the email verification notification
     *
     * @return JsonResponse
     */
    public function resendEmailVerification(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $user->sendEmailVerificationNotification();
        $data = [
            'message' => 'Email verification sent',
            'email' => $user->email
        ];

        return $this->success($data, Response::HTTP_OK);
    }

    /**
     * Forgot password request
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(AuthRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return $this->error(
                'Unable to send password reset email',
                Response::HTTP_FAILED_DEPENDENCY,
                ApiErrorCode::DEPENDENCY_ERROR
            );
        }

        $data = ['message' => 'Email verification sent', 'email' => $request->get('email')];
        return $this->success($data, Response::HTTP_OK);
    }

    /**
     * Forgot password request
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function resetPassword(AuthRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error(
                'Unable to reset password',
                Response::HTTP_BAD_REQUEST,
                ApiErrorCode::BAD_REQUEST
            );
        }

        $data = ['message' => 'Password reset was successful'];
        return $this->success($data, Response::HTTP_OK);
    }
}
