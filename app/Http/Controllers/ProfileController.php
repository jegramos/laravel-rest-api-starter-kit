<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileController extends ApiController
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Fetch the user information of the currently authenticated user
     *
     * @return JsonResponse
     */
    public function view(): JsonResponse
    {
        $user = User::findOrFail(auth()->user()->id)->load('userProfile');
        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Update the authenticated user's profile
     *
     * @param ProfileRequest $request
     * @return JsonResponse
     */
    public function update(ProfileRequest $request): JsonResponse
    {
        $user = $this->userService->update(auth()->user()->id, $request->validated());
        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    public function changePassword(ProfileRequest $request): JsonResponse
    {
        return $this->success(null, Response::HTTP_OK);
    }

    /**
     * Upload profile picture
     *
     * @param ProfileRequest $request
     * @param CloudFileServiceInterface $uploader
     * @return JsonResponse
     */
    public function uploadProfilePicture(ProfileRequest $request, CloudFileServiceInterface $uploader): JsonResponse
    {
        $userId = auth()->user()->id;

        $file = $request->file('photo');
        $result = $uploader->upload($userId, $file, 'images', 'profile-pictures');
        $this->userService->update($userId, ['profile_picture_path' => $result['path']]);

        return $this->success(['data' => $result], Response::HTTP_OK);
    }
}
