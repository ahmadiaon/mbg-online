<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterLevel extends Model
{
    use HasFactory;
    protected $fillable = [
        'tanggal',
        'jam',
        'lokasi',
        'tinggi',
        'foto_panorama',
        'foto_draft_meter',
    ];
}
