<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Data extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'temperature',
        'humidity',
        'heat_index',
        'latitude',
        'longitude',
        'altitude',
        'date',
        'time',
        'luminosity'
    ];
}
