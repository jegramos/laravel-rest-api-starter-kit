<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userRepository->all();
        return $this->success($users, Response::HTTP_OK);
    }

    /**
     * Persist a user record
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = $this->userRepository->create($request->validated());
        return $this->success($user, Response::HTTP_CREATED);
    }
}
