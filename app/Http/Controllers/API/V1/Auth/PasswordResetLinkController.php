<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Password Reset",
 *     description="Endpoints for requesting password reset links"
 * )
 */
class PasswordResetLinkController extends Controller
{
    use ApiResponse;
    /**
     * Handle an incoming password reset link request.
     *
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Send password reset link",
     *     description="Sends a password reset link to the user's email address.",
     *     operationId="sendPasswordResetLink",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password reset link request data",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="We have emailed your password reset link!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or unable to send reset link",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="We can't find a user with that email address.")
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status != Password::RESET_LINK_SENT) {
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }
            return $this->formatSuccessResponse(null, "Password reset link successfuly sent!", 200, $request);
        } catch (ValidationException $e) {
            return $this->handleValidationError($e, $request);
        }
    }
}
