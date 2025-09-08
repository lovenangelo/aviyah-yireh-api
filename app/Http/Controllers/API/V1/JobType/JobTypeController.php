<?php

namespace App\Http\Controllers\API\V1\JobType;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobType\StoreJobTypeRequest;
use App\Http\Requests\JobType\UpdateJobTypeRequest;
use App\Models\JobType;
use App\Traits\ApiResponse;

class JobTypeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $jobTypes = JobType::all();

            return $this->formatSuccessResponse($jobTypes);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobTypeRequest $request)
    {
        try {
            $jobType = JobType::create($request->validated());

            return $this->formatSuccessResponse($jobType, 201);
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
            $jobType = JobType::find($id);

            return $this->formatSuccessResponse($jobType);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobTypeRequest $request, $id)
    {
        $jobType = JobType::find($id);
        try {
            if (empty($jobType)) {
                return $this->formatErrorResponse('Job type not found', 404);
            }
            $jobType->update($request->validated());

            return $this->formatSuccessResponse($jobType);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaborCategory $jobType)
    {
        try {
            $jobType->delete();

            return $this->formatSuccessResponse(['message' => 'Deleted successfully']);
        } catch (\Throwable $th) {
            return $this->formatErrorResponse($th->getMessage(), 500);
        }
    }
}
