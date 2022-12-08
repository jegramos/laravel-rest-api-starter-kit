<?php

namespace App\Services\HttpResources;

use App\Enums\PaginationType;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
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

    /** @inheritDoc */
    public function all(?PaginationType $pagination = null): Collection|Paginator|LengthAwarePaginator|CursorPaginator
    {
        /** @var Builder $users */
        $users = $this->model->withFilters();

        $limit = request('limit') ?? 25;
        return match ($pagination) {
            PaginationType::LENGTH_AWARE => $users->paginate($limit),
            PaginationType::SIMPLE => $users->simplePaginate($limit),
            PaginationType::CURSOR => $users->cursorPaginate($limit),
            default => $users->get(),
        };
    }

    /**
     * @inheritDoc
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

            $user = User::create($userCredentials);

            $userRoles = empty($userInfo['roles']) ? ['standard_user'] : $userInfo['roles'];
            $user->syncRoles($userRoles);
            // Spatie automatically attaches a roles attr after syncing
            // we don't want it as there is too much clutter sent
            unset($user['roles']);

            $exemptedAttributes = ['email', 'username', 'password', 'active', 'email_verified_at'];
            $user->userProfile()->create(Arr::except($userInfo, $exemptedAttributes));

            return $user->load('userProfile');
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * @inheritDoc
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

            $user->save();
            $user->refresh();

            return $user;
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function updateProfile($id, array $newUserInfo): User
    {
        return DB::transaction(function () use ($id, $newUserInfo) {
            /** @var User $user */
            $user = $this->model::with('userProfile')->findOrFail($id);

            $user->update(Arr::only($newUserInfo, ['email', 'username']));
            $user->userProfile()->update(Arr::except($newUserInfo, ['email', 'username']));

            $user->save();
            $user->refresh();

            return $user;
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }
}
