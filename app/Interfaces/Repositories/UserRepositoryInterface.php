<?php

namespace App\Interfaces\Repositories;

use App\Enums\PaginationType;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    /**
     * Fetch a list of users
     *
     * @param PaginationType|null $paginationType
     * @param Request $request
     * @return array
     */
    public function all(?PaginationType $paginationType = PaginationType::LENGTH_AWARE): array;

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
     */
    public function update($id, array $newUserInfo): array;

    /**
     * Delete a user
     *
     * @param $id
     */
    public function destroy($id): array;
}
