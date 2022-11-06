<?php

namespace App\Interfaces\Repositories;

interface UserRepositoryInterface
{
    /**
     * Fetch a list of users
     *
     * @param array|null $filters
     * @param bool $paginated
     * @return array
     */
    public function all(array $filters = null, bool $paginated = false): array;

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
