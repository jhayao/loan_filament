<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'slug',
        'email',
        'interest_rate',
        'max_branch',
        'max_area',
        'avatar_url',
    ];

    protected static function booted(): void
    {
        static::created(function (Company $company) {
            $company->setting()->create();
        });
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class)->chaperone();
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->chaperone();
    }

    public function staffs(): MorphMany
    {
        return $this->morphMany(Staff::class, 'assignable');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->attributes['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function staff(): MorphMany
    {
        return $this->morphMany(Staff::class, 'assignable');
    }


}
