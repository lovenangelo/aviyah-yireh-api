<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Traits\ApiErrorResponse;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

/**
 * @OA\Tag(
 *     name="Registration",
 *     description="Endpoints for user registration"
 * )
 */
class RegisteredUserController extends Controller
{
    use ApiResponse;

    /**
     * Handle an incoming registration request.
     *
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     description="Registers a new user and returns user data and token for token-based requests, or no content for session-based requests.",
     *     operationId="registerUser",
     *     tags={"Registration"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based registration request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful (token-based)",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="token", type="string", example="1|abc123def456...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Registration successful (session-based, no content)"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
            ]);

            event(new Registered($user));

            Auth::login($user);

            $userLogin = Auth::user();
            $userLogin->logRegister(true);
            $token = $user->createToken('auth-token')->plainTextToken;
            $data = ["user" => $user, "token" => $token];
            return $this->formatSuccessResponse($data, "Successfully registered new user", 201);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'registration');
        }
    }
}
