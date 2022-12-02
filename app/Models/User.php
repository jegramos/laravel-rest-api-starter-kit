<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use DateTimeHelper;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use CascadeSoftDeletes;

    /**
     * Requirement by Spatie Laravel Permissions when setting multiple auth guards
     * @see https://spatie.be/docs/laravel-permission/v5/basic-usage/multiple-guards
     */
    public $guard_name = 'sanctum';

    /** @see https://github.com/shiftonelabs/laravel-cascade-deletes */
    protected array $cascadeDeletes = ['userProfile'];
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'active',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Dynamic computed attributes
     *
     * @var array<int, string>
     */
    protected $appends = [
        'attached_roles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (User $user) {
            $user->email = DateTimeHelper::appendTimestamp($user->email, '::deleted_');
            $user->username = DateTimeHelper::appendTimestamp($user->username, '::deleted_');
            $user->saveQuietly();
        });
    }

    /**
     * A User has exactly one profile information
     *
     * @return HasOne
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @Attribute
     * Hash the password whenever it is changed
     *
     * @return Attribute
     */
    public function password(): Attribute
    {
        return Attribute::set(
            fn ($value) => Hash::make($value)
        );
    }

    /**
     * @Attribute
     * Attach role names with their IDs
     *
     * @return Attribute
     */
    public function attachedRoles(): Attribute
    {
        return Attribute::get(function () {
            return $this->roles()->get()->map(fn (Role $role) => ['id' => $role->id, 'name' => $role->name]);
        });
    }
}
