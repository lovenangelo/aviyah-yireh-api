<?php

namespace App\Http\Controllers\API\V1\ItemCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemCategoryRequest;
use App\Http\Requests\UpdateItemCategoryRequest;
use App\Http\Resources\CustomPaginatedCollection;
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
            $categories = ItemCategory::all();
            return $this->formatSuccessResponse($categories);
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
            $paginated = new CustomPaginatedCollection($itemCategory);
            return $this->formatSuccessResponse($paginated, 201);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemCategory $itemCategory)
    {
         try {
            return $this->formatSuccessResponse($itemCategory);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemCategory $itemCategory)
    {
        try {
            $itemCategory->update($request->validated());
            return $this->formatSuccessResponse($itemCategory);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $itemCategory)
    {
        try {
            $itemCategory->delete();
            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
