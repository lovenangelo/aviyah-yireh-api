<?php

namespace App\Http\Controllers\API\V1\LaborCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaborCategory\StoreLaborCategoryRequest;
use App\Http\Requests\LaborCategory\UpdateLaborCategoryRequest;
use App\Models\LaborCategory;
use App\Traits\ApiResponse;

class LaborCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $company = LaborCategory::with('company')->get();

            return $this->formatSuccessResponse($company);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLaborCategoryRequest $request)
    {
        try {
            $laborCategory = LaborCategory::create($request->validated());

            return $this->formatSuccessResponse($laborCategory, 201);
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
            $labor_category = LaborCategory::find($id);

            return $this->formatSuccessResponse($labor_category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLaborCategoryRequest $request, $id)
    {
        $labor_category = LaborCategory::find($id);
        try {
            if (empty($labor_category)) {
                return $this->formatErrorResponse('Labor Category not found', 404);
            }
            $labor_category->update($request->validated());

            return $this->formatSuccessResponse($labor_category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaborCategory $laborCategory)
    {
        try {
            $laborCategory->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
