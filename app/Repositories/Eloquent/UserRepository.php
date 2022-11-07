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

    /**
     * @inheritDoc
     */
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
        return DB::transaction(function () use ($userInfo) {
            $user = User::create([
                'email' => $userInfo['email'],
                'username' => $userInfo['username'],
                'password' => $userInfo['password']
            ]);

            $exemptedAttributes = ['email', 'username', 'password'];
            $user->userProfile()->create(Arr::except($userInfo, $exemptedAttributes));

            return $user->load('userProfile')->toArray();
        }, self::MAX_TRANSACTION_DEADLOCK_ATTEMPTS);
    }

    /**
     * @inheritDoc
     */
    public function read($id, bool $include_profile = true): array
    {
        $query = User::query();

        if ($include_profile) {
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

            $user->update(Arr::only($newUserInfo, ['email', 'username', 'password']));
            $user->userProfile()->update(Arr::except($newUserInfo, ['email', 'username', 'password']));
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
