<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityReadings extends Model
{
    use HasFactory;

    static $tableName = 'electricity_readings';
     public $timestamps = true;

    protected $fillable = [
        'reading',
        'time',
        'smart_meter_id'
    ];

    public function smartMeter()
    {
        return $this->belongsTo(SmartMeter::class, 'smart_meter_id');
    }


}
