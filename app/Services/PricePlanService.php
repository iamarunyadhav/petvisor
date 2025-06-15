<?php

namespace App\Services;

use App\Exceptions\InvalidMeterIdException;
use App\Repository\IPricePlanRepository;

class PricePlanService
{
    private $meterReadingService;
    private $pricePlanRepository;

    public function __construct(MeterReadingService $meterReadingService, IPricePlanRepository $pricePlanRepository)
    {
        $this->meterReadingService = $meterReadingService;
        $this->pricePlanRepository = $pricePlanRepository;
    }

    /**
     * @throws InvalidMeterIdException
     */
    public function getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId): ?array
    {
        $getCostForAllPlans = [];
        $readings = $this->meterReadingService->getReadings($smartMeterId);

        $pricePlans = $this->pricePlanRepository->getPricePlans();
        foreach ($pricePlans as $pricePlan) {
            $getCostForAllPlans[] = array('supplier' => $pricePlan['supplier'], 'cost' => $this->calculateCost($readings, $pricePlan));
        }

        return $getCostForAllPlans;
    }

    /**
     * @throws InvalidMeterIdException
     */
    public function getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId): ?array
    {
        $costPricePerPlans = $this->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
        $currentAvailableSupplierIds = $this->pricePlanRepository->getCurrentAvailableSupplierIds($smartMeterId);

        $currentSupplierIdForSmartMeterID = [];
        foreach ($currentAvailableSupplierIds as $currentAvailableSupplierId) {
            if ($currentAvailableSupplierId->smartMeterId = $smartMeterId) {
                $currentSupplierIdForSmartMeterID = $currentAvailableSupplierId->supplier;
            }
        }

        return [
            "priceComparisons" => $costPricePerPlans,
            "currentSupplier" => $currentSupplierIdForSmartMeterID,
        ];
    }

    /**
     * @throws InvalidMeterIdException
     */
    private function calculateCost($electricityReadings, $pricePlan)
    {
        $average = $this->calculateAverageReading($electricityReadings);
        $timeElapsed = $this->calculateTimeElapsed($electricityReadings);
        // dd($average, $timeElapsed);
        $averagedCost = $average / $timeElapsed;
        // dd($averagedCost);
        return $averagedCost * $pricePlan['unit_rate'];
    }

    /**
     * @throws InvalidMeterIdException
     */
    private function calculateAverageReading($electricityReadings)
    {
        if (count($electricityReadings) <= 0) throw new InvalidMeterIdException("test");

        $newSummedReadings = 0;
        foreach ($electricityReadings as $electricityReading) {
            // foreach ($electricityReading as $reading) {
                $newSummedReadings += (float)$electricityReading->reading;
            // }
        }
        return $newSummedReadings / count($electricityReadings);
    }

    private function calculateTimeElapsed($electricityReadings)
    {
        $readingHours = [];
        foreach ($electricityReadings as $electricityReading) {
            // foreach ($electricityReading as $time) {
                $readingHours[] = $electricityReading->time;
            // }
        }
        $minimumElectricityReading = strtotime(min($readingHours));
        $maximumElectricityReading = strtotime(max($readingHours));

        if(count($readingHours)<2)
        {
            return 1;
        }

        $diff= abs($maximumElectricityReading - $minimumElectricityReading) / (60 * 60);

        return $diff>0? $diff : 1;
    }
}
