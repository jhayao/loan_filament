<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'address',
        'registration_number',
        'tax_id',
        'phone_number',
        'email',
        'website',
        'logo',
        'status',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class)->chaperone();
    }

}
