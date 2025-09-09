<?php

namespace App\Http\Controllers\API\V1\ServiceCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategory\StoreServiceCategoryRequest;
use App\Http\Requests\ServiceCategory\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use App\Traits\ApiResponse;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $company = ServiceCategory::with('company')->get();

            return $this->formatSuccessResponse($company);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCategoryRequest $request)
    {
        try {
            $serviceCategory = ServiceCategory::create($request->validated());

            return $this->formatSuccessResponse($serviceCategory, 201);
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
            $category = ServiceCategory::find($id);

            return $this->formatSuccessResponse($category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCategoryRequest $request, $id)
    {
        $service_category = ServiceCategory::find($id);
        try {
            if (empty($service_category)) {
                return $this->formatErrorResponse('Item Category not found', 404);
            }
            $service_category->update($request->validated());

            return $this->formatSuccessResponse($service_category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        try {
            $serviceCategory->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
