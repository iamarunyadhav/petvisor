<?php

namespace App\Providers;

use App\Helpers\ModelHelper;
use App\Models\MeterReadings;
use App\Repository\ElectricityReadingRepository;
use App\Repository\IElectricityReadingRepository;
use App\Repository\IPeaktimeMultiplierRepository;
use App\Repository\IPricePlanRepository;
use App\Repository\PeaktimeMultiplierRepository;
use App\Repository\PricePlanRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IElectricityReadingRepository::class, function (){
            return new ElectricityReadingRepository();
        });

        $this->app->bind(IPricePlanRepository::class, function (){
            return new PricePlanRepository();
        });

        $this->app->bind(IPeaktimeMultiplierRepository::class, function (){
            return new PeaktimeMultiplierRepository();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $data = array("id" => 1, "entries" => [["name" => "san", "value" => 100], ["name" => "sandy", "value" => 200]]);
        config(['joyconfig.data' => $data]);
    }
}
