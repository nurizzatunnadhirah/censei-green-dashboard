<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'waste_record_id',
        'file_name',
        'file_type',
        'file_size',
        'file_path',
        'uploaded_at',
    ];

    public function wasteRecord(): BelongsTo
    {
        return $this->belongsTo(WasteRecord::class);
    }
}