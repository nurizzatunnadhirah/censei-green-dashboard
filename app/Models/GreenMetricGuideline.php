<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GreenMetricGuideline extends Model
{
    protected $fillable = [
        'metric_name',
        'description',
    ];
}