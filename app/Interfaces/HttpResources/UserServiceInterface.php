<?php

namespace App\Interfaces\HttpResources;

use App\Enums\PaginationType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;

interface UserServiceInterface
{
    /**
     * Fetch a list of users
     *
     * @param PaginationType|null $pagination
     * @return Collection|Paginator|LengthAwarePaginator|CursorPaginator
     */
    public function all(?PaginationType $pagination = null): Collection|Paginator|LengthAwarePaginator|CursorPaginator;

    /**
     * Create a new user
     *
     * @param array $userInfo
     * @return User
     */
    public function create(array $userInfo): User;

    /**
     * Update an existing user
     *
     * @param $id
     * @param array $newUserInfo
     * @return User
     */
    public function update($id, array $newUserInfo): User;

    /**
     * Update the profile information of a user
     *
     * @param $id
     * @param array $newUserInfo
     * @return User
     */
    public function updateProfile($id, array $newUserInfo): User;

    /**
     * Search for a user
     *
     * @param string $term
     * @param PaginationType|null $pagination
     * @return Collection|Paginator|LengthAwarePaginator|CursorPaginator
     */
    public function search(
        string $term,
        ?PaginationType $pagination = null
    ): Collection|Paginator|LengthAwarePaginator|CursorPaginator;
}
