<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

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
        'country',
        'profile_picture_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'user_id',
    ];

    /**
     * Dynamic computed attributes
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name'
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
     * Create full_name attribute
     *
     * @return Attribute
     */
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
}
