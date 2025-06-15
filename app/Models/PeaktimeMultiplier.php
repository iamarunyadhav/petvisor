<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeaktimeMultiplier extends Model
{
    use HasFactory;
    protected $table = 'peaktime_multipliers';
    public $timestamps = true;

    protected $fillable = ['dayofWeek','multiplier','price_plan_id'];


    public function pricePlan()
    {
        return $this->belongsTo(PricePlan::class, 'price_plan_id');
    }

}
