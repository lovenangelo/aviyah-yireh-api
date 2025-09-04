<?php

namespace App\Http\Controllers\API\V1\ItemSetUp;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemSetUp\StoreItemSetUpRequest;
use App\Http\Requests\ItemSetUp\UpdateItemSetUpRequest;
use App\Models\ItemSetUp;
use App\Traits\ApiResponse;

class ItemSetUpController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $item_setup = ItemSetUp::all();

            return $this->formatSuccessResponse($item_setup);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemSetUpRequest $request)
    {
        try {
            $itemCategory = ItemSetUp::create($request->validated());

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
            $category = ItemSetUp::find($id);

            return $this->formatSuccessResponse($category);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemSetUpRequest $request, $id)
    {
        $item_setup = ItemSetUp::find($id);
        try {
            if (empty($item_setup)) {
                return $this->formatErrorResponse('Item Category not found', 404);
            }

            $item_setup->update($request->validated());

            return $this->formatSuccessResponse($item_setup);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemSetUp $itemSetUp)
    {
        try {
            $itemSetUp->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
