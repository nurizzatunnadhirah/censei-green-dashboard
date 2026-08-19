<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'location_name',
        'description',
    ];

    public function wasteRecords(): HasMany
    {
        return $this->hasMany(WasteRecord::class);
    }
}