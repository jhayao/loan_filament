<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model implements HasAvatar
{
    use HasFactory;

    protected $fillable = [
        'interest_rate',
        'max_branch',
        'max_area',
        'avatar_url',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
