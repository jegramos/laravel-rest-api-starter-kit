<?php

namespace App\Models;

use App\Notifications\Auth\QueuedResetPasswordNotification;
use App\Notifications\Auth\QueuedVerifyEmailNotification;
use App\QueryFilters\Generic\Active;
use App\QueryFilters\Generic\Sort;
use App\QueryFilters\User\Email;
use App\QueryFilters\User\Role;
use App\QueryFilters\User\Username;
use App\QueryFilters\User\Verified;
use DateTimeHelper;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens;
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use CascadeSoftDeletes;

    /**
     * @Library
     * Requirement by Spatie Laravel Permissions when setting multiple auth guards
     *
     * @see https://spatie.be/docs/laravel-permission/v5/basic-usage/multiple-guards
     */
    public string $guard_name = 'sanctum';

    /**
     * @Library
     * @see https://github.com/shiftonelabs/laravel-cascade-deletes
     */
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
        'email_verified_at',
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
     * The attributes that should be eager-loaded
     *
     * @var array<int, string>
     */
    protected $with = [
        'roles:id,name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (User $user) {
            $user->email = DateTimeHelper::appendTimestamp($user->email, '::deleted_');
            $user->username = DateTimeHelper::appendTimestamp($user->username, '::deleted_');
            $user->saveQuietly();
        });
    }

    /**
     * @Scope
     * Pipeline for HTTP query filters
     */
    public function scopeFiltered(Builder $builder): Builder
    {
        return app(Pipeline::class)
            ->send($builder->with('userProfile'))
            ->through([
                Active::class,
                Sort::class,
                Username::class,
                Email::class,
                Verified::class,
                Role::class,
            ])
            ->thenReturn();
    }

    /**
     * A User has exactly one profile information
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @Attribute
     * Hash the password whenever it is changed
     */
    public function password(): Attribute
    {
        return Attribute::set(fn ($value) => Hash::make($value));
    }

    /**
     * Set username to lowercase
     */
    public function username(): Attribute
    {
        return Attribute::set(fn ($value) => strtolower($value));
    }

    /**
     * Set username to lowercase
     */
    public function email(): Attribute
    {
        return Attribute::set(fn ($value) => strtolower($value));
    }

    /*
     * Override default email verification notification
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmailNotification($this));
    }

    /*
     * Override default password reset notification
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPasswordNotification($token));
    }

    /**
     * @SlackIntegration
     * Route notifications for the Slack channel.
     */
    public function routeNotificationForSlack(Notification $notification): string
    {
        return config('integrations.slack.webhooks.dev-alerts');
    }
}
