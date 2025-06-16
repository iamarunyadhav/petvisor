<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidMeterIdException;
use App\Models\PeaktimeMultiplier;
use App\Models\SmartMeter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PeaktimeMultiplierService;

class PeaktimeMultiplierController extends Controller
{
    private $peaktimeMultiplierService;

    public function __construct(PeaktimeMultiplierService $peaktimeMultiplierService)
    {
        $this->peaktimeMultiplierService = $peaktimeMultiplierService;
    }

    public function index($planId = null)
    {
        try {
            $peaktimes = $this->peaktimeMultiplierService->getPeaktime($planId);
            return response()->json($peaktimes);
        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }
        if ($peaktimes->isEmpty()) {
            return response()->json("No peaktime multipliers available", Response::HTTP_NOT_FOUND);
        }

        return response()->json($peaktimes, Response::HTTP_OK);
    }

    public function indexAll()
    {

        try {
            $peaktimes = $this->peaktimeMultiplierService->getPeaktimeAll();
            return response()->json($peaktimes);
        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }


        if ($peaktimes->isEmpty()) {
            return response()->json("No peaktime multipliers available", Response::HTTP_NOT_FOUND);
        }

        return response()->json($peaktimes, Response::HTTP_OK);
    }

    public function store(Request $request,$planId)
    {
        // Validate the request data
        $request->validate([
            'dayofWeek' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'multiplier' => 'required|numeric|min:1',
        ]);

        $isValidPlan=$this->peaktimeMultiplierService->validatePricePlanId($planId);
        if (!$isValidPlan) {
            return response()->json(['error' => 'Invalid price plan ID'], Response::HTTP_BAD_REQUEST);
        }
        // Create a new PeaktimeMultiplier instance
         try {
            $peaktime = $this->peaktimeMultiplierService->createPeak($request,$planId);
        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }

        if ($peaktime) {
            return response()->json("Peak time inserted successfully", 201);
        }

        return response()->json("No peak time available to insert", Response::HTTP_BAD_REQUEST);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $request->validate([
            'dayofWeek' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'multiplier' => 'required|numeric|min:1',
        ]);
        try {
            $peaktime = $this->peaktimeMultiplierService->updatePeak($id, $request);
        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }
        if ($peaktime) {
            return response()->json("Peak time updated successfully", 200);
        }
        return response()->json("No peak time available to update", Response::HTTP_BAD_REQUEST);

    }

    public function destroy($id)
    {
        try{
            $peaktime = $this->peaktimeMultiplierService->getPeaktime($id);
            dd($peaktime);
            if ($peaktime->isEmpty()) {
                return response()->json("No peaktime multipliers available with ID: " . $id, Response::HTTP_NOT_FOUND);
            }
            $peaktime = $peaktime->first();
            if (!$peaktime) {
                return response()->json("Peaktime multiplier not found with ID: " . $id, Response::HTTP_NOT_FOUND);
            }
            $deleted = $this->peaktimeMultiplierService->deletePeaktime($id);
            if ($deleted) {
                return response()->json("Peak time deleted successfully", 200);
            }
            return response()->json("No peak time available to delete", Response::HTTP_BAD_REQUEST);
        }
        catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }
    }
}
