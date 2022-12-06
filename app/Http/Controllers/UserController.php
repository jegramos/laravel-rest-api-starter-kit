<?php

namespace App\Http\Controllers;

use App\Enums\PaginationType;
use App\Events\UserCreated;
use App\Http\Requests\UserRequest;
use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Listeners\SendWelcomeEmailNotification;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use PaginationHelper;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->all(PaginationType::LENGTH_AWARE);
        $formatted = PaginationHelper::formatPagination($users);
        return $this->success($formatted, Response::HTTP_OK);
    }

    /**
     * Persist a user record
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());
        UserCreated::dispatch($user);
        return $this->success(['data' => $user], Response::HTTP_CREATED);
    }

    /**
     * Fetch a single user's details
     *
     * @param $id
     * @return JsonResponse
     */
    public function read($id): JsonResponse
    {
        $user = User::with('userProfile')->findOrFail($id);
        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Update a user
     *
     * @param $id
     * @param UserRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update($id, UserRequest $request): JsonResponse
    {
        if (!$this->authorize('update', User::findOrFail($id))) {
            throw new AuthorizationException('A Super User cannot be updated');
        }

        $user = $this->userService->update($id, $request->validated());
        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Delete a user
     *
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        if (!$this->authorize('update', User::findOrFail($id))) {
            throw new AuthorizationException('A Super User cannot be deleted');
        }

        User::findOrFail($id)->delete();
        return $this->success(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Upload profile picture
     *
     * @param $id
     * @param UserRequest $request
     * @param CloudFileServiceInterface $uploader
     * @return JsonResponse
     */
    public function uploadProfilePicture($id, UserRequest $request, CloudFileServiceInterface $uploader): JsonResponse
    {
        $file = $request->file('photo');
        $result = $uploader->upload($id, $file, 'images', 'profile-pictures');
        $this->userService->update($id, ['profile_picture_path' => $result['path']]);

        return $this->success(['data' => $result], Response::HTTP_OK);
    }
}
