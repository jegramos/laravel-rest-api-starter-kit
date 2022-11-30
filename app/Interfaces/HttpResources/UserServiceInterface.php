<?php

namespace App\Interfaces\HttpResources;

use App\Enums\PaginationType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

interface UserServiceInterface
{
    /**
     * Fetch a list of users
     *
     * @param Request $request
     * @param PaginationType|null $paginationType
     * @return Collection|Paginator|LengthAwarePaginator|CursorPaginator
     */
    public function all(
        Request $request,
        ?PaginationType $paginationType = null
    ): Collection|Paginator|LengthAwarePaginator|CursorPaginator;

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
}
