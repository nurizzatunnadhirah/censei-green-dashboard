<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WasteRecord extends Model
{
    protected $fillable = [
        'location_id',
        'waste_type_id',
        'recorded_by',
        'actual_weight',
        'collection_method',
        'collection_date',
        'recycled',
        'remarks',

        'waste_stream',
        'vehicle_no',
        'time_in',
        'time_out',
    ];

    public function location(): BelongsTo
    {
    return $this->belongsTo(Location::class);
   }

    public function wasteType(): BelongsTo
    {
        return $this->belongsTo(WasteType::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(WasteFile::class);
    }
}