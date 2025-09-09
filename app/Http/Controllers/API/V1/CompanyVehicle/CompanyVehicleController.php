<?php

namespace App\Http\Controllers\API\V1\CompanyVehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyVehicle\StoreCompanyVehicleRequest;
use App\Http\Requests\CompanyVehicle\UpdateCompanyVehicleRequest;
use App\Models\CompanyVehicle;
use App\Traits\ApiResponse;

class CompanyVehicleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $companies = CompanyVehicle::with('company')->get();

            return $this->formatSuccessResponse($companies);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyVehicleRequest $request)
    {
        try {
            $vehicle = CompanyVehicle::create($request->validated());

            return $this->formatSuccessResponse($vehicle, 201);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $vehicle = CompanyVehicle::find($id);

            return $this->formatSuccessResponse($vehicle);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyVehicleRequest $request, $id)
    {
        $vehicle = CompanyVehicle::find($id);
        try {
            if (empty($vehicle)) {
                return $this->formatErrorResponse('Vehicle not found', 404);
            }

            $vehicle->update($request->validated());

            return $this->formatSuccessResponse($vehicle);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyVehicle $vehicle)
    {
        try {
            $vehicle->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
