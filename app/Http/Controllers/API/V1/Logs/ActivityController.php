<?php

namespace App\Http\Controllers\API\V1\Logs;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomPaginatedCollection;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    use ApiResponse;
    public function getAllLogs(): JsonResponse
    {
        try {
            $perPage = request()->get('per_page', 15);
            $activities = Activity::paginate($perPage);

            // Assuming you have a CustomPaginatedCollection resource
            return $this->formatSuccessResponse(
                new CustomPaginatedCollection($activities),
                "Activity logs retrieved successfully"
            );
        } catch (\Throwable $e) {
            return $this->handleApiException($e, request(), "Fetching Activity Logs");
        }
    }
}
