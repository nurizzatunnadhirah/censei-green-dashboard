<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'faculty_name',
        'faculty_code',
    ];
        public function wasteRecords(): HasMany
    {
        return $this->hasMany(WasteRecord::class);
    }
}
