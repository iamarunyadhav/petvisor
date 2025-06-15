<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricePlan extends Model
{
    use HasFactory;

    protected $table = 'price_plans';
    public $timestamps = true;
    protected $fillable = [
        'plan_name',
        'supplier',
        'unit_rate'
    ];
    public function smartMeters()
    {
        return $this->hasMany(SmartMeter::class, 'price_plan_id');
    }

    public function peaktimeMultipliers()
    {
        return $this->hasMany(PeaktimeMultiplier::class, 'price_plan_id');
    }


}
