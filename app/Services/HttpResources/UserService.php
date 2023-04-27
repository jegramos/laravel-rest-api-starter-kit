<?php

namespace App\Services\HttpResources;

use App\Enums\PaginationType;
use App\Enums\Role;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService implements UserServiceInterface
{
    public const MAX_TRANSACTION_DEADLOCK_ATTEMPTS = 5;

    private User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /** {@inheritDoc} */
    public function all(?PaginationType $pagination = null): Collection|Paginator|LengthAwarePaginator|CursorPaginator
    {
        /** @var Builder $users */
        $users = $this->model->filtered();

        return $this->buildPagination($pagination, $users);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     */
    public function create(array $userInfo): User
    {
        return DB::transaction(function () use ($userInfo) {
            $userCredentials = [
                'email' => $userInfo['email'],
                'username' => $userInfo['username'],
                'password' => $userInfo['password'],
            ];

            if (isset($userInfo['active'])) {
                $userCredentials['active'] = $userInfo['active'];
            }

            if (isset($userInfo['email_verified'])) {
                $userCredentials['email_verified_at'] = $userInfo['email_verified'] ? Carbon::now('utc') : null;
            }

            $user = $this->model::create($userCredentials);

            $userRoles = empty($userInfo['roles']) ? [Role::STANDARD_USER->value] : $userInfo['roles'];
            $user->syncRoles($userRoles);
            $user = $user->fresh();

            $exemptedAttributes = ['email', 'username', 'password', 'active', 'email_verified_at'];
            $user->userProfile()->create(Arr::except($userInfo, $exemptedAttributes));

            return $user->load('userProfile');
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     */
    public function update($id, array $newUserInfo): User
    {
        return DB::transaction(function () use ($id, $newUserInfo) {
            /** @var User $user */
            $user = $this->model::with('userProfile')->findOrFail($id);

            unset($newUserInfo['password_confirmation']);

            if (isset($newUserInfo['email_verified'])) {
                $newUserInfo['email_verified_at'] = $newUserInfo['email_verified'] ? Carbon::now('utc') : null;
                unset($newUserInfo['email_verified']);
            }

            $user->update(Arr::only($newUserInfo, ['email', 'username', 'password', 'active', 'email_verified_at']));
            $user->userProfile()->update(
                Arr::except($newUserInfo, ['email', 'username', 'password', 'active', 'email_verified_at', 'roles'])
            );

            if (isset($newUserInfo['roles'])) {
                $user->syncRoles($newUserInfo['roles']);
            }

            $user->refresh();

            return $user;
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     */
    public function updateProfile($id, array $newUserInfo): User
    {
        return DB::transaction(function () use ($id, $newUserInfo) {
            /** @var User $user */
            $user = $this->model::with('userProfile')->findOrFail($id);

            $user->update(Arr::only($newUserInfo, ['email', 'username']));
            $user->userProfile()->update(Arr::except($newUserInfo, ['email', 'username']));
            $user->refresh();

            return $user;
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * Search user via email, username, last_name, first_name, and middle_name
     */
    public function search(
        string $term,
        ?PaginationType $pagination = null
    ): Collection|Paginator|LengthAwarePaginator|CursorPaginator {
        $users = $this->model::query()
            ->with('userProfile')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')

            // Do a prefix match for username and email to preserve indexing performance
            ->where('users.email', 'like', "$term%")
            ->orWhere('users.username', 'like', "$term%")

            // Do a full match search for the names as they have a fullText index in our migrations
            ->orWhere('user_profiles.first_name', 'like', "%$term%")
            ->orWhere('user_profiles.last_name', 'like', "%$term%")
            ->orWhere('user_profiles.middle_name', 'like', "%$term%");

        return $this->buildPagination($pagination, $users);
    }

    /**
     * Build pagination
     */
    private function buildPagination(
        ?PaginationType $pagination,
        Builder $builder
    ): Paginator|Collection|LengthAwarePaginator|CursorPaginator {
        $limit = request('limit') ?? 25;

        return match ($pagination) {
            PaginationType::LENGTH_AWARE => $builder->paginate($limit),
            PaginationType::SIMPLE => $builder->simplePaginate($limit),
            PaginationType::CURSOR => $builder->cursorPaginate($limit),
            default => $builder->get(),
        };
    }
}
