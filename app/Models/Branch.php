<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function staff(): MorphMany
    {
        return $this->morphMany(Staff::class, 'assignable');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->chaperone();
    }

    public function staffs(): MorphMany
    {
        return $this->morphMany(Staff::class, 'assignable');
    }

}
