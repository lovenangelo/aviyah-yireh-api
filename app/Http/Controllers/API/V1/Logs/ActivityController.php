<?php

namespace App\Http\Controllers\API\V1\Logs;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Requests\Event\BulkDeleteEventRequest;
use App\Http\Resources\CustomPaginatedCollection;
use App\Repositories\EventRepository;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
class ActivityController extends Controller
{
    public function getAllLogs()
    {
        $activities = Activity::all();
        return response()->json($activities);
    }


}
