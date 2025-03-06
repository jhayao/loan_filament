<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'company_id',
        'slug',
        'status',
        'address',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
