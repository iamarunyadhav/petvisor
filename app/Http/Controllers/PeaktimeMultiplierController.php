<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidMeterIdException;
use App\Models\ElectricityReadings;
use App\Models\PeaktimeMultiplier;
use App\Models\SmartMeter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PeaktimeMultiplierService;
use Carbon\Carbon;

class PeaktimeMultiplierController extends Controller
{
    public function index($planId)
    {
      return PeaktimeMultiplier::where('id',$planId)->get();
    }

    public function indexAll()
    {
        return PeaktimeMultiplier::all();
    }

    public function store(Request $request,$planId)
    {
        //validte the source
        $data=$request->validate([
           'dayofWeek'=>'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
           'multiplier'=>'required|numeric|min:1'
        ]);
        $data['price_plan_id']=$planId;

        $created=PeaktimeMultiplier::create($data);
        if($created)
        {
            return response()->json('success',200);
        }
         return response()->json('fail',400);
    }

    public function update(Request $request,$id)
    {
        //validte the source
        $data=$request->validate([
           'dayofWeek'=>'required|string',
           'multiplier'=>'required|numeric|min:1'
        ]);

        $multiplier=PeaktimeMultiplier::findOrFail($id);
        $multiplier->update($data);

        return response()->json($multiplier);
    }

    public function delete($id)
    {
        $multiplier=PeaktimeMultiplier::findOrFail($id);
        $multiplier->delete();

        return response()->json(['status'=>'success'],204);

    }

    public function calculateBill(Request $request)
    {
        $smartMeterId = $request->input('smartMeterId');
        $from = Carbon::parse($request->input('from'))->format('Y-m-d H:i:s');
        $to = Carbon::parse($request->input('to'))->format('Y-m-d H:i:s');

        $readings = ElectricityReadings::where('smart_meter_id', $smartMeterId)
            ->whereBetween('time', [$from, $to])
            ->get();

        $smartMeter = SmartMeter::find($smartMeterId);
        if (!$smartMeter) return response()->json(['error' => 'Smart meter not found'], 404);

        $plan = $smartMeter->pricePlan;
        if (!$plan) return response()->json(['error' => 'Price plan not found'], 404);

        $multipliers = PeaktimeMultiplier::where('price_plan_id', $plan->id)->get()->keyBy('dayofWeek');

        $total = 0;
        foreach ($readings as $reading) {

            $day = Carbon::parse($reading->time)->format('l'); // 'Monday', etc
            $multiplier = $multipliers[$day]->multiplier ?? 1; // If no multiplier, use 1
            $cost = $reading->reading * $plan->unit_rate * $multiplier;
            $total += $cost;
        }


        return response()->json(['total' => $total]);
    }


}

