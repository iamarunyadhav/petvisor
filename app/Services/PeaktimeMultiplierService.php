<?php

namespace App\Services;

use App\Exceptions\InvalidMeterIdException;
use App\Models\SmartMeter;
use App\Repository\IPeaktimeMultiplierRepository;

class PeaktimeMultiplierService
{
   private $peaktimeMultiplierRepository;

    public function __construct(IPeaktimeMultiplierRepository $peaktimeMultiplierRepository)
    {
        $this->peaktimeMultiplierRepository = $peaktimeMultiplierRepository;
    }

    public function validatePricePlanId($pricePlanId):bool
    {

       $isValid = $this->peaktimeMultiplierRepository->validatePricePlanId($pricePlanId);
       return $isValid;
    }

     public function createPeak($request,$planId)    {
        //  if ($createPeak->isEmpty()) {
        //     throw new InvalidMeterIdException("No electricity readings available for " . $createPeak);
        // }

        $PeaktimeMultiplierArray = array(
            'dayofWeek' => $request->input('dayofWeek'),
            'multiplier' => $request->input('multiplier'),
            'price_plan_id' => $planId
        );

        return $this->peaktimeMultiplierRepository->createPeak($PeaktimeMultiplierArray);

    }


    public function getPeaktime($planId)
    {
        if ($planId) {
            $peaktimes = $this->peaktimeMultiplierRepository->getPeaktimeByPlanId($planId);
        } else {
            $peaktimes = $this->peaktimeMultiplierRepository->getAllPeaktimes();
        }
        return $peaktimes;
    }
}
