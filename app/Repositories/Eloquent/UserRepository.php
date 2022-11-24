<?php

namespace App\Repositories\Eloquent;

use App\Enums\PaginationType;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\QueryFilters\Active;
use App\QueryFilters\Sort;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class UserRepository implements UserRepositoryInterface
{
    public const MAX_TRANSACTION_DEADLOCK_ATTEMPTS = 5;

    /**
     * @inheritDoc
     */
    public function all(?PaginationType $paginationType = null): array
    {
        /** @var Builder $users */
        $users = app(Pipeline::class)
            ->send(User::query()->with('userProfile'))
            ->through([
                Sort::class,
                Active::class
            ])
            ->thenReturn();

        $limit = request('limit');
        return match ($paginationType) {
            PaginationType::LENGTH_AWARE => $users->paginate($limit)->toArray(),
            PaginationType::SIMPLE => $users->simplePaginate($limit)->toArray(),
            PaginationType::CURSOR => $users->cursorPaginate($limit)->toArray(),
            default => $users->get()->toArray(),
        };
    }

    /**
     * Return all users with pagination
     *
     * @param array|null $filters
     * @param PaginationType $paginationType
     * @return void
     */
    public function allPaginated(array $filters = null, PaginationType $paginationType = PaginationType::LENGTH_AWARE)
    {
        $limit = request('limit');

        /** @var Builder $users */
        $users = app(Pipeline::class)
            ->send(User::query()->with('userProfile'))
            ->through([
                Sort::class,
                Active::class
            ])
            ->thenReturn();

        $results = [];
        /** @var LengthAwarePaginator $users */
        switch ($paginationType) {
            case PaginationType::LENGTH_AWARE:
                $results = $users->paginate($limit)->toArray();
                break;
            case PaginationType::SIMPLE:
                $results = $users->simplePaginate($limit)->toArray();
                break;
            case PaginationType::CURSOR:
                $results = $users->cursorPaginate($limit)->toArray();
                break;
        }

        return $results;
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

            if (isset($userInfo['email_verified'])) {
                $userCredentials['email_verified_at'] = $userInfo['email_verified'] ? Carbon::now('utc') : null;
            }

            $user = User::create($userCredentials);

            $exemptedAttributes = ['email', 'username', 'password', 'active', 'email_verified_at'];
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

            unset($newUserInfo['password_confirmation']);

            if (isset($newUserInfo['email_verified'])) {
                $newUserInfo['email_verified_at'] = $newUserInfo['email_verified'] ? Carbon::now('utc') : null;
                unset($newUserInfo['email_verified']);
            }

            $user->update(Arr::only($newUserInfo, ['email', 'username', 'password', 'active', 'email_verified_at']));
            $user->userProfile()->update(
                Arr::except($newUserInfo, ['email', 'username', 'password', 'active', 'email_verified_at'])
            );
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
