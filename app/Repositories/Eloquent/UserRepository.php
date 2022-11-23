<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\QueryFilters\Active;
use App\QueryFilters\Sort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PaginationHelper;
use Throwable;

class UserRepository implements UserRepositoryInterface
{
    public const MAX_TRANSACTION_DEADLOCK_ATTEMPTS = 5;

    /**
     * @inheritDoc
     */
    public function all(array $filters = null, bool $paginated = false): array
    {
        /** @var LengthAwarePaginator $users */
        $users = app(Pipeline::class)
            ->send(User::query()->with('userProfile'))
            ->through([
                Sort::class,
                Active::class
            ])
            ->thenReturn()
            ->paginate(request('limit'));

        return $users->toArray();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function create(array $userInfo): array
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

            $user = User::create($userCredentials);

            $exemptedAttributes = ['email', 'username', 'password', 'active'];
            $user->userProfile()->create(Arr::except($userInfo, $exemptedAttributes));

            return $user->load('userProfile')->toArray();
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * @inheritDoc
     */
    public function read($id, bool $includeProfile = true): array
    {
        $query = User::query();

        if ($includeProfile) {
            $query->with('userProfile');
        }

        return $query->findOrFail($id)->toArray();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function update($id, array $newUserInfo): array
    {
        return DB::transaction(function () use ($id, $newUserInfo) {
            /** @var User $user */
            $user = User::with('userProfile')->findOrFail($id);

            $user->update(Arr::only($newUserInfo, ['email', 'username', 'password', 'active']));
            $user->userProfile()->update(Arr::except($newUserInfo, ['email', 'username', 'password', 'active']));
            $user->save();

            $user->refresh();

            return $user->toArray();
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function destroy($id): array
    {
        $user = User::with('userProfile')->findOrFail($id);
        $user->delete();

        return $user->toArray();
    }
}
