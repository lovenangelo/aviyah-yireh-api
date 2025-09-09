<?php

namespace App\Http\Controllers\API\V1\ItemCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCategory\StoreItemCategoryRequest;
use App\Http\Requests\ItemCategory\UpdateItemCategoryRequest;
use App\Models\ItemCategory;
use App\Traits\ApiResponse;

class ItemCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $company = ItemCategory::with('company')->get();

            return $this->formatSuccessResponse($company);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemCategoryRequest $request)
    {
        try {
            $itemCategory = ItemCategory::create($request->validated());

            return $this->formatSuccessResponse($itemCategory, 201);
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
            $category = ItemCategory::find($id);

            return $this->formatSuccessResponse($category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemCategoryRequest $request, $id)
    {
        $category = ItemCategory::find($id);
        try {
            if (empty($category)) {
                return $this->formatErrorResponse('Item Category not found', 404);
            }
            $category->update($request->validated());

            return $this->formatSuccessResponse($category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemCategory $itemCategory)
    {
        try {
            $itemCategory->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
