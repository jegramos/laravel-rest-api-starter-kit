<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends ApiController
{
    /**
     * Get email availability
     *
     * @param AvailabilityRequest $request
     * @return JsonResponse
     */
    public function getEmailAvailability(AvailabilityRequest $request): JsonResponse
    {
        $email = strtolower($request->get('value'));
        $isAvailable = !User::whereEmail($email)->first();
        $data = ['is_available' => $isAvailable];

        return $this->success(['data' => $data], Response::HTTP_OK);
    }

    /**
     * Get username availability
     * @param AvailabilityRequest $request
     * @return JsonResponse
     */
    public function getUsernameAvailability(AvailabilityRequest $request): JsonResponse
    {
        $username = strtolower($request->get('value'));
        $isAvailable = !User::whereUsername($username)->first();
        $data = ['is_available' => $isAvailable];

        return $this->success(['data' => $data], Response::HTTP_OK);
    }
}
