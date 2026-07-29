<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WasteCategory extends Model
{
    protected $fillable = [
        'category_name',
        'description',
    ];

    public function wasteTypes(): HasMany
    {
        return $this->hasMany(WasteType::class);
    }
}