<?php

namespace App\Repository;

use App\Models\ElectricityReadings;
use App\Models\SmartMeter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ElectricityReadingRepository implements IElectricityReadingRepository
{
    public function getElectricityReadings($smartMeterId): Collection
    {
        // return DB::table(ElectricityReadings::$tableName)
        //     ->join('smart_meters', 'electricity_readings.smart_meter_id', '=', 'smart_meters.id')
        //     ->where('smart_meters.smartMeterId', '=', $smartMeterId)
        //     ->get(['time', 'reading']);

        return ElectricityReadings::whereHas('smartMeter', function ($query) use ($smartMeterId) {
            $query->where('smartMeterId',$smartMeterId);
        })->get(['time', 'reading']);
    }

    public function getSmartMeterId($smartMeterId)
    {
        // return SmartMeter::query()->where('smart_meters.smartMeterId', '=', $smartMeterId)
        //     ->first('smart_meters.id');
             return SmartMeter::where('smartMeterId',$smartMeterId)
            ->first('smart_meters.id');
    }

    public function insertElectricityReadings($electricityReadingArray): bool
    {
        // return ElectricityReadings::query()->insert($electricityReadingArray);
         $create=ElectricityReadings::create($electricityReadingArray);
        return $create ? true : false;
    }

    public function insertSmartMeter($smartMeter): int
    {
        // return SmartMeter::query()->insertGetId($smartMeter);
        $create=SmartMeter::create($smartMeter);
        return $create ? $create->id : 0;

    }

    public function updateSmartMeter($planId,$id): int
    {
        $updated = SmartMeter::where('id', $id)->update(['price_plan_id' => $planId]);
        return $updated ? $id : 0;
    }
}
