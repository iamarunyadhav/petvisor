<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartMeter extends Model
{
    use HasFactory;

    protected $table = 'smart_meters';
    public $timestamps = true;

    protected $fillable = [
        'smartMeterId',
        'price_plan_id'
    ];

    public function electricityReadings()
    {
        return $this->hasMany(ElectricityReadings::class, 'smart_meter_id');
    }

    public function pricePlan()
    {
        return $this->belongsTo(PricePlan::class, 'price_plan_id');
    }
}
