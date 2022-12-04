<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
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
}
