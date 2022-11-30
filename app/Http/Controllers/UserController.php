<?php

namespace App\Http\Controllers;

use App\Enums\PaginationType;
use App\Http\Requests\UserRequest;
use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use App\Interfaces\HttpResources\UserServiceInterface;
use Illuminate\Http\JsonResponse;
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
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function index(UserRequest $request): JsonResponse
    {
        $users = $this->userService->all($request, PaginationType::LENGTH_AWARE);
        return $this->success($users, Response::HTTP_OK, [], PaginationType::LENGTH_AWARE);
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
        return $this->success($user, Response::HTTP_CREATED);
    }

    /**
     * Fetch a single user's details
     *
     * @param $id
     * @return JsonResponse
     */
    public function read($id): JsonResponse
    {
        $user = $this->userService->read($id);
        return $this->success($user, Response::HTTP_OK);
    }

    /**
     * Update a user
     *
     * @param $id
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function update($id, UserRequest $request): JsonResponse
    {
        $user = $this->userService->update($id, $request->validated());
        return $this->success($user, Response::HTTP_OK);
    }

    /**
     * Delete a user
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = $this->userService->destroy($id);
        return $this->success($user, Response::HTTP_OK);
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

        return $this->success($result, Response::HTTP_OK);
    }
}
