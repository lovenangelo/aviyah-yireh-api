<?php

namespace App\Http\Controllers\API\V1\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\Service;
use App\Traits\ApiResponse;

class ServiceController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = Service::with(['company', 'service_category'])->get();

            return $this->formatSuccessResponse($services);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            $service = Service::create($request->validated());

            return $this->formatSuccessResponse($service, 201);
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
            $service = Service::find($id);

            return $this->formatSuccessResponse($service);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        $service = Service::find($id);
        try {
            if (empty($service)) {
                return $this->formatErrorResponse('Service not found', 404);
            }

            $service->update($request->validated());

            return $this->formatSuccessResponse($service);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            $service->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
