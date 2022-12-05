<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
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

        $tokens = $user->tokens->map(function (PersonalAccessToken $token) {
            return [
                'id' => $token->getAttribute('id'),
                'name' => $token->getAttribute('name'),
                'expires_at' => $token->getAttribute('expires_at'),
                'last_used_at' => $token->getAttribute('last_used_at'),
                'created_at' => $token->getAttribute('created_at')
            ];
        });

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
     * Response if the email is not verified
     *
     * @return JsonResponse
     */
    public function emailVerificationNotice(): JsonResponse
    {
        return $this->error('Email address not verified', Response::HTTP_UNAUTHORIZED);
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
}
