<?php

namespace App\Repository;


use Illuminate\Support\Collection;

interface IPeaktimeMultiplierRepository
{

    /**
     * @param $smartMeterID
     * @return bool
     */
    function validatePricePlanId($pricePlanId): bool;

    function createPeak($PeaktimeMultiplierArray);

    function getPeaktimeByPlanId($planId): Collection;
    function getAllPeaktimes(): Collection;

}
