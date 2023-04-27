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
     */
    public function all(?PaginationType $pagination = null): Collection|Paginator|LengthAwarePaginator|CursorPaginator;

    /**
     * Create a new user
     */
    public function create(array $userInfo): User;

    /**
     * Update an existing user
     */
    public function update($id, array $newUserInfo): User;

    /**
     * Update the profile information of a user
     */
    public function updateProfile($id, array $newUserInfo): User;

    /**
     * Search for a user
     */
    public function search(
        string $term,
        ?PaginationType $pagination = null
    ): Collection|Paginator|LengthAwarePaginator|CursorPaginator;
}
