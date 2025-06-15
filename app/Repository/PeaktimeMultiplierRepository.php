<?php

namespace App\Repository;

use App\Models\PeaktimeMultiplier;
use App\Models\PricePlan;
use App\Models\SmartMeter;
use App\Repository\IPeaktimeMultiplierRepository;
use Illuminate\Support\Collection;


class PeaktimeMultiplierRepository implements IPeaktimeMultiplierRepository
{

    function validatePricePlanId($pricePlanId): bool
    {
       return PricePlan::where('id', $pricePlanId)->exists();
    }

    function createPeak($PeaktimeMultiplierArray)
    {
        // dd($PeaktimeMultiplierArray);
       $exists = PeaktimeMultiplier::where('price_plan_id', $PeaktimeMultiplierArray['price_plan_id'])
            ->where('dayofWeek', $PeaktimeMultiplierArray['dayofWeek'])
            ->exists();
        // dd($exists);

        if ($exists) {
            return response()->json(['error' => 'Already exists for this plan and day'], 422);
        }
        else{
            $created= PeaktimeMultiplier::create($PeaktimeMultiplierArray);
            return $created ? 'success' : 'failed';
        }

        // return PeaktimeMultiplier::where('price_plan_id', $PeaktimeMultiplierArray->planId)->get();
    }


    function getPeaktimeByPlanId($planId): Collection
    {
        return PeaktimeMultiplier::where('price_plan_id', $planId)->get();
    }
    function getAllPeaktimes(): Collection
    {
        return PeaktimeMultiplier::all();
    }

}


?>
