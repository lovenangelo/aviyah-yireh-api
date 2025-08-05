<?php

namespace App\Http\Controllers\API\V1\Event;

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

class EventController extends Controller
{
    private const EVENT_NOT_FOUND = "Event not found";
    use ApiResponse;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', null);
            $filters = $request->only([
                'title',
                'location',
                'start_at',
                'end_at',
                'author_id'
            ]);

            if ($filters) {
                $event = $this->eventRepository->getFilter($filters, $perPage);
            } else {
                $event =  $this->eventRepository->getEvents($perPage);
            }

            $event = new CustomPaginatedCollection($event, $request->query('include_links', false));

            return $this->formatSuccessResponse(
                data: $event
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Show Events');
        }
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        try {


            $this->authorize('create', Events::class);

            $event = $this->eventRepository->storeEvents($request);


            return $this->formatSuccessResponse(
                message: "Event created successfully",
                data: $event
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Create Event');
        }
    }

    public function show($id, Request $request): JsonResponse
    {
        try {

            $event = $this->eventRepository->find($id);

            if (!$event) {
                return $this->formatErrorResponse(
                    code: "NOT_FOUND",
                    message: self::EVENT_NOT_FOUND,
                    statusCode: 404
                );
            }

            return $this->formatSuccessResponse(
                data: $event
            );
        } catch (\Throwable $th) {
            return $this->handleApiException(
                $th,
                $request,
                'Get Event'
            );
        }
    }

    public function update(UpdateEventRequest $request,  $id)
    {
        try {
            $event = $this->eventRepository->find($id);

            if (!$event) {
                return $this->formatErrorResponse(
                    code: "NOT_FOUND",
                    message: self::EVENT_NOT_FOUND,
                    statusCode: 404
                );
            }

            $this->authorize('update', $event);


            $this->eventRepository->update($request->all(), $event->id);

            return $this->formatSuccessResponse(
                message: "Event update successfully",
                data: [
                    "eventId" => $event->id,
                    "changes" => $request->all()
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Update Event');
        }
    }

    public function destroy($id, Request $request)
    {
        try {

            $event = $this->eventRepository->find($id);

            if (!$event) {
                return $this->formatErrorResponse(
                    code: "NOT_FOUND",
                    message: self::EVENT_NOT_FOUND,
                    statusCode: 404
                );
            }

            $this->authorize('delete', $event);

            $this->eventRepository->delete($event->id);

            return $this->formatSuccessResponse(
                message: "Event successfully deleted",
                data: [
                    "deletedEvent" => [
                        "id" => $event->id,
                        "title" => $event->title
                    ]
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, "Delete Event");
        }
    }

    public function bulkDestroy(BulkDeleteEventRequest $request)
    {
        try {

            $this->authorize('bulkDelete', User::class);

            $result = $this->eventRepository->bulkDestroy($request->validated('ids'));

            return $this->formatSuccessResponse(
                message: "Events deleted successfully",
                data: $result
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Bulk Delete Events');
        }
    }
}
