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
        }
        return $peaktimes;
    }

    public function getPeaktimeAll()
    {
        $peaktimes = $this->peaktimeMultiplierRepository->getAllPeaktimes();
        return $peaktimes;
    }


    public function updatePeak($id, $request)
    {
        // dd($id, $request);
        $peaktime = $this->peaktimeMultiplierRepository->getPeaktimeById($id);
        if (!$peaktime) {
            throw new InvalidMeterIdException("Peaktime multiplier not found with ID: " . $id);
        }

        $peaktime->dayofWeek = $request->input('dayofWeek', $peaktime->dayofWeek);
        $peaktime->multiplier = $request->input('multiplier', $peaktime->multiplier);

        // dd($peaktime->toArray());

        return $this->peaktimeMultiplierRepository->updatePeaktime($id,$peaktime->toArray());
    }
    public function deletePeaktime($id): bool
    {
        return $this->peaktimeMultiplierRepository->deletePeaktime($id);
    }
}
