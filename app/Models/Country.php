<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iso',
        'name',
        'iso3',
        'num_code',
    ];

    /**
     *
     * @return HasOne
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }
}
