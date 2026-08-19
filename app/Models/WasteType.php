<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WasteType extends Model
{
    protected $fillable = [
        'category_id',
        'type_name',
        'description',
    ];

    public function category()
{
    return $this->belongsTo(
        WasteCategory::class,
        'category_id'
    );
}

    public function wasteRecords(): HasMany
    {
        return $this->hasMany(WasteRecord::class);
    }
}