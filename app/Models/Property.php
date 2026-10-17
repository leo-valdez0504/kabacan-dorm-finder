<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['landlord_id', 'name', 'description', 'address', 'latitude', 'longitude', 'rules', 'is_verified'])]
class Property extends Model
{
    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }
}
