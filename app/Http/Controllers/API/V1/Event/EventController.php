<?php

namespace App\Http\Controllers\API\V1\Event;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Repositories\EventRepository;
use App\Models\User;


class EventController extends Controller
{
    private const EVENT_NOT_FOUND = "Event not found";
    use ApiResponse;

    public function __construct(EventRepository $eventRepository){
        $this->eventRepository = $eventRepository;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $event =  $this->eventRepository->getEvents();
            return $this->formatSuccessResponse(
                data: $event
            );
        } catch (\Throwable $th) {
           return $this->handleApiException($th, $request, 'Show Events');
        }

    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request): JsonResponse
    {
        try {
            
            $event = $this->eventRepository->find($id);

            if(!$event){
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
            $th,$request, 'Get Event'
           );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request,  $id)
    {
        try {
            $event = $this->eventRepository->find($id);

            if(!$event){
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
                    "eventId"=>$event->id,
                    "changes" => $request->all()
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Update Event');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        try {
        
            $event = $this->eventRepository->find($id);

            if(!$event){
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
                    "deletedEvent" =>[
                        "id" => $event->id,
                        "title"=>$event->title
                    ]
                ]
                );

        } catch (\Throwable $th) {
           return $this->handleApiException($th, $request, "Delete Event");
        }
    }
}
