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
