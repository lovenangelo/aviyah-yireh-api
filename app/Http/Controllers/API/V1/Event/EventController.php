<?php

namespace App\Http\Controllers\API\V1\Event;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\User;

class EventController extends Controller
{
    private const EVENT_NOT_FOUND = "Event not found";
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $event = Events::all();
            return $this->formatSuccessResponse(
                data: $event
            );
        } catch (\Throwable $th) {
           return $this->handleApiExeception($th, $request, 'Show Events');
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return $this->handleAuthorizationError($request);
            }
            $this->authorize('create', Events::class);

            $event = Events::create([
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'author_id' => auth()->id(),
            ]);


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
            
            $event = Events::find($id);
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
            $event = Events::find($id);

            if(!$event){
                return $this->formatErrorResponse(
                    code: "NOT_FOUND",
                    message: self::EVENT_NOT_FOUND,
                    statusCode: 404
                );
            }

            $lastEventTitle = $event->title;

            $this->authorize('update', $event);

           
            $event->update($request->all());

            return $this->formatSuccessResponse(
                message: "Event update successfully",
                data: [
                    "updatedEvent"=>$lastEventTitle,
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
        
            $event = Events::find($id);

            if(!$event){
                return $this->formatErrorResponse(
                    code: "NOT_FOUND",
                    message: self::EVENT_NOT_FOUND,
                    statusCode: 404
                );
            }

            $this->authorize('delete', $event);
            $event->delete();
            return $this->formatSuccessResponse(
                message: "Event successfully deleted",
                data: [
                    "deletedEvent"=>$event->title
                ]
                );

        } catch (\Throwable $th) {
           return $this->handleApiException($th, $request, "Delete Event");
        }
    }
}
