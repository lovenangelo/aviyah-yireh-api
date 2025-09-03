<?php

namespace App\Http\Controllers\API\V1\Tax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\StoreTaxRequest;
use App\Http\Requests\Tax\UpdateTaxRequest;
use App\Http\Resources\CustomPaginatedCollection;
use App\Models\Tax;
use App\Traits\ApiResponse;

class TaxController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $taxes = Tax::all();

            return $this->formatSuccessResponse($taxes);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxRequest $request)
    {
        try {
            $tax = Tax::create($request->validated());
            $paginated = new CustomPaginatedCollection($tax);

            return $this->formatSuccessResponse($paginated, 201);
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
            $tax = Tax::find($id);

            return $this->formatSuccessResponse($tax);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxRequest $request, $id)
    {
        $tax = Tax::find($id);
        try {
            $tax->update($request->validated());

            return $this->formatSuccessResponse($tax);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        try {
            $tax->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
