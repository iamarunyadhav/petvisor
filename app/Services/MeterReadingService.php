<?php

namespace App\Services;

use App\Exceptions\InvalidMeterIdException;
use App\Repository\IElectricityReadingRepository;
use App\Repository\IPricePlanRepository;
use Illuminate\Support\Collection;

class MeterReadingService
{
    private $electricityReadingRepository;
    private $pricePlanRepository;

    public function __construct(IElectricityReadingRepository $electricityReadingRepository, IPricePlanRepository $pricePlanRepository)
    {
        $this->electricityReadingRepository = $electricityReadingRepository;
        $this->pricePlanRepository = $pricePlanRepository;
    }

    /**
     * @throws InvalidMeterIdException
     */
    public function getReadings($smartMeterId): Collection
    {
        $electricityReadings = $this->electricityReadingRepository->getElectricityReadings($smartMeterId);
        if ($electricityReadings->isEmpty()) {
            throw new InvalidMeterIdException("No electricity readings available for " . $smartMeterId);
        }
        return $electricityReadings;
    }


    /**
     * @throws InvalidMeterIdException
     */
    public function storeReadings($smartMeterId, $readings,$planId=null): bool
    {
        // dd($smartMeterId, $readings,$planId);
        $result = false;
        $this->validateSmartMeterId($smartMeterId);

        $smartIDFromDb = $this->electricityReadingRepository->getSmartMeterId($smartMeterId);

        foreach ($readings as $reading) {

            if ($smartIDFromDb != null && $smartIDFromDb->id > 0) {
                $result = $this->insertDataIntoElectricityReadings($reading, $smartIDFromDb->id);

                if($planId)
                {
                    // dd($planId,$smartIDFromDb->id);
                    $insertedSmartMeterId = $this->electricityReadingRepository->updateSmartMeter($planId,$smartIDFromDb->id);
                }
            } else {
                $randomPricePlanIdFromDB = $this->pricePlanRepository->getRandomPricePlanId();
                $planId=$planId ? $planId:$randomPricePlanIdFromDB->id;

                if ($randomPricePlanIdFromDB != null && $randomPricePlanIdFromDB->id > 0) {
                    $smartMeter = array(
                        'smartMeterId' => $smartMeterId,
                        'price_plan_id' => $planId,
                        'created_at' => now(),
                        'updated_at' => now()
                        );
                    $insertedSmartMeterId = $this->electricityReadingRepository->insertSmartMeter($smartMeter);

                    if ($insertedSmartMeterId > 0) {
                        $smartIDFromDb=(object)['id'=>$insertedSmartMeterId];
                        $result = $this->insertDataIntoElectricityReadings($reading, $insertedSmartMeterId);
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param $reading
     * @param int $smartIDFromDb
     * @return bool
     */
    private function insertDataIntoElectricityReadings($reading, int $smartIDFromDb): bool
    {
        $carbonTime = \Carbon\Carbon::parse($reading['time'])->format('Y-m-d H:i:s');

        $electricityReadingArray = array(
         'reading' => $reading['reading'],
         'time' => $carbonTime,
         'smart_meter_id' => $smartIDFromDb,
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s'));
        return $this->electricityReadingRepository->insertElectricityReadings($electricityReadingArray);
    }


    /**
     * @throws InvalidMeterIdException
     */
    private function validateSmartMeterId($smartMeterId): void
    {
        $smartMeterIdPattern = "/^smart-meter-\d+$/";
        if (preg_match($smartMeterIdPattern, $smartMeterId) == 0) {
            throw new InvalidMeterIdException("Smart meter id should follow defined pattern (Ex: smart-meter-1)");
        }
    }

}
