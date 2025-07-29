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

/**
 * @OA\Tag(
 *     name="Events",
 *     description="Endpoints for managing events, including CRUD operations and retrieving events associated with users."
 * )
 */


class EventController extends Controller
{
    private const EVENT_NOT_FOUND = "Event not found";
    use ApiResponse;

    public function __construct(EventRepository $eventRepository){
        $this->eventRepository = $eventRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/v1/event",
     *     summary="Get list of events",
     *     security={{"bearer_token":{}}},
     *     tags={"Events"},
     *     @OA\Parameter(name="title", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="location", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="start_at", in="query", required=false, @OA\Schema(type="string",format="date-time")),
     *     @OA\Parameter(name="end_at", in="query", required=false, @OA\Schema(type="string",format="date-time")),
     *     @OA\Parameter(name="author_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of events")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'title',
                'location',
                'start_at',
                'end_at',
                'author_id'
            ]);

            if($filters){
                $event = $this->eventRepository->getFilter($filters);
            }
            else{
                $event =  $this->eventRepository->getEvents();
            }
           
            return $this->formatSuccessResponse(
                data: $event
            );
        } catch (\Throwable $th) {
           return $this->handleApiException($th, $request, 'Show Events');
        }

    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *  path="/api/v1/event",
     *  summary="Create Event",
     *  security={{"bearer_token":{}}},
     *  tags={"Events"},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"title", "description","location", "start_at", "end_at"},
     *          @OA\Property(property="title", type="string"),
     *          @OA\Property(property="description", type="string"),
     *          @OA\Property(property="location", type="string"),
     *          @OA\Property(property="start_at", type="string", format="date-time"),
     *          @OA\Property(property="end_at", type="string", format="date-time")
     *      )
     * ),
     * @OA\Response(response=201, description="Event created successfully"),
     * 
     * )
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
     * @OA\Get(
     * path="/api/v1/event/{id}",
     * summary="Get an event by ID",
     * security={{"bearer_token":{}}},
     * tags={"Events"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Event Details"),
     * @OA\Response(response=404, description="Event not found")
     * )
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
     * @OA\Put(
     *  path="/api/v1/event/{id}",
     *  summary="Update an Event",
     *  tags={"Events"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={},
     *          @OA\Property(property="title", type="string"),
     *          @OA\Property(property="description", type="string"),
     *          @OA\Property(property="location", type="string"),
     *          @OA\Property(property="start_at", type="string", format="date-time"),
     *          @OA\Property(property="end_at", type="string", format="date-time")
     *         )
     *      ),
     * @OA\Response(response=200, description="Event updated successfully"),
     * @OA\Response(response=404, description="Event not found")
     * 
     * )
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
     * @OA\Delete(
     * path="/api/v1/event/{id}",
     * summary="Delete an Event by ID",
     * tags={"Events"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Event deleted successfully. Contains details of the deleted event"),
     * @OA\Response(response=404, description="Event not found")
     * 
     * 
     * )
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
