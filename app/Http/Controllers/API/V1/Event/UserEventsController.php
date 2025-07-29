<?php

namespace App\Http\Controllers\API\V1\Event;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



class UserEventsController extends Controller
{
    use ApiResponse;
    /**
     * Display all users and their associated created events
     * @OA\Get(
     * path="/api/v1/event/users",
     * summary="List all users who have created events",
     * tags={"Events"},
     * security={{"bearer_token":{}}},
     * @OA\Response(response=200, description="List users with their created events")
     * )
     */
    public function index(Request $request)
    {
        try {
            $id = auth()->id();
            
            $this->authorize('viewAny', User::find($id));
            $userEvents = User::with('events')->select('id', 'name')->has('events')->get();

            return $this->formatSuccessResponse(
                data: $userEvents
            );

        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Show Created Events By User');
        }
    }

   

    /**
     * Display specific user and their associated created events
     * @OA\Get(
     * path="/api/v1/event/user/{id}",
     * summary="Get a specific User  with their events",
     * tags={"Events"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="User with events details"),
     * @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $user = User::with('events')->select('id', 'name')->where('id', $id)->first();
            $currentUser = auth()->id();

            if(!$user){
                return $this->formatErrorResponse(
                       code: "NOT_FOUND",
                       message: "User not found",
                       statusCode: 404
                );
            }

            $this->authorize('view', User::find($currentUser));
            return $this->formatSuccessResponse(
                data: $user
            );
        


        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Show User with Event');
        }
    }

}
