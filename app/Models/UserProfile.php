<?php

namespace App\Models;

use App\Enums\Sex;
use App\Interfaces\Services\CloudFileManager\CanGenerateTempUrl;
use App\Services\CloudFileManager\S3FileManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'mobile_number',
        'telephone_number',
        'sex',
        'birthday',
        'address_line_1', // Building number, Building name
        'address_line_2', // Street, Road name
        'address_line_3', // // Additional address info
        'district', // Barangay, Village
        'city', // Or Municipality
        'province',
        'postal_code',
        'country_id',
        'profile_picture_path',
    ];

    /**
     * Relationships to eager-load
     *
     * @var array<int, string>
     */
    protected $with = [
        'country'
    ];

    /**
     * Dynamic computed attributes
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name', 'profile_picture_url'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'country_id',
        'profile_picture_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'sex' => Sex::class, // Laravel 9 enum casting. @see https://laravel.com/docs/9.x/releases
    ];

    /**
     * A profile belongs to exactly one user
     *
     * @returns BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A profile belongs to exactly one country
     *
     * @returns BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /** @Attribute */
    public function fullName(): Attribute
    {
        return Attribute::get(function () {
            $firstName = $this->first_name;
            $lastName = $this->last_name;
            $middle_name = $this->middle_name;

            if ($middle_name) {
                return "$firstName $middle_name $lastName";
            }

            return "$firstName $lastName";
        });
    }

    /**
     * @Attribute
     */
    public function profilePictureUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->profile_picture_path) {
                return null;
            }

            // resolve a tmp url generator instance from the service container
            $tmpUrlGenerator = resolve(CanGenerateTempUrl::class);

            // generate a tmp URL available for 3-minutes
            return $tmpUrlGenerator->getTmpUrl($this->profile_picture_path, 3 * 60);
        });
    }
}
