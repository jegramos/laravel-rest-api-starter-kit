<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use App\Http\Requests\ProfileRequest;
use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends ApiController
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Fetch the user information of the currently authenticated user
     */
    public function view(): JsonResponse
    {
        $user = User::findOrFail(auth()->user()->id)->load('userProfile');

        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Update the authenticated user's profile
     */
    public function update(ProfileRequest $request): JsonResponse
    {
        $user = $this->userService->update(auth()->user()->id, $request->validated());

        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(ProfileRequest $request, CloudFileServiceInterface $uploader): JsonResponse
    {
        $userId = auth()->user()->id;

        $file = $request->file('photo');
        $result = $uploader->upload($userId, $file, 'images', 'profile-pictures');
        $this->userService->update($userId, ['profile_picture_path' => $result['path']]);

        return $this->success(['data' => $result], Response::HTTP_OK);
    }

    /**
     * Change user password
     */
    public function changePassword(ProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (! Hash::check($request->get('old_password'), $user->password)) {
            return $this->error(
                'Old password is incorrect',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiErrorCode::INCORRECT_OLD_PASSWORD
            );
        }

        $user->password = $request->password;
        $user->save();

        return $this->success(['message' => 'Password changed successfully'], Response::HTTP_OK);
    }
}
