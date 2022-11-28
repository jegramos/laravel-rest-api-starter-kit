<?php

namespace App\Interfaces\HttpResources;

use App\Enums\PaginationType;
use Illuminate\Http\Request;

interface UserServiceInterface
{
    /**
     * Fetch a list of users
     *
     * @param Request $request
     * @param PaginationType|null $paginationType
     * @return array
     */
    public function all(Request $request, ?PaginationType $paginationType = null): array;

    /**
     * Create a new user
     *
     * @param array $userInfo
     * @return array
     */
    public function create(array $userInfo): array;

    /**
     * Fetch a single user
     *
     * @param $id
     * @return array
     */
    public function read($id): array;

    /**
     * Update an existing user
     *
     * @param $id
     * @param array $newUserInfo
     * @return array
     */
    public function update($id, array $newUserInfo): array;

    /**
     * Delete a user
     *
     * @param $id
     * @return array
     */
    public function destroy($id): array;
}
