<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends ApiController
{
    /**
     * Fetch the user information of the currently authenticated user
     *
     * @return JsonResponse
     */
    public function view(): JsonResponse
    {
        $user = auth()->user;
        return $this->success(['data' => $user], Response::HTTP_OK);
    }

    public function update(): JsonResponse
    {
        return $this->success(['data' => null], Response::HTTP_OK);

    }
}
