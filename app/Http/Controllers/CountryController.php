<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends ApiController
{
    /**
     * Retrieve all countries
     *
     * @return JsonResponse
     */
    public function fetch(): JsonResponse
    {
        $countries = Country::all();
        return $this->success(['data' => $countries], Response::HTTP_OK);
    }
}
