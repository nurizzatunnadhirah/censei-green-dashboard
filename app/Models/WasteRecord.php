<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WasteRecord extends Model
{
    protected $fillable = [
        'faculty_id',
        'waste_type_id',
        'recorded_by',
        'actual_weight',
        'collection_method',
        'collection_date',
        'recycled',
        'remarks',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function wasteType(): BelongsTo
    {
        return $this->belongsTo(WasteType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(WasteFile::class);
    }
}