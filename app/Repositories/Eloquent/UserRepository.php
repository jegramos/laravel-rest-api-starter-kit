<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserRepository implements UserRepositoryInterface
{
    public const MAX_TRANSACTION_DEADLOCK_ATTEMPTS = 5;

    /** @inheritDoc */
    public function all(array $filters = null, bool $paginated = false): array
    {
        return User::with('userProfile')->get()->toArray();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function create(array $userInfo): array
    {
        DB::transaction(function () {
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
        return [];
    }

    /** @inheritDoc */
    public function read($id): array
    {
        return [];
    }

    /** @inheritDoc */
    public function update($id, array $newUserInfo): array
    {
        return [];
    }

    /** @inheritDoc */
    public function destroy($id): array
    {
        return [];
    }
}
