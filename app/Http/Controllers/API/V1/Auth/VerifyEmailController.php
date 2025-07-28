<?php

namespace App\Http\Controllers\API\V1\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Traits\ApiResponse;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints for verifying user email addresses"
 * )
 */
class VerifyEmailController extends Controller
{
    use ApiResponse;
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @OA\Get(
     *     path="/api/v1/verify-email/{id}/{hash}",
     *     summary="Verify user email address",
     *     description="Verifies the user's email address using the verification link.",
     *     operationId="verifyEmail",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Email verification hash",
     *         @OA\Schema(type="string", example="abcdef123456")
     *     ),
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based verification request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified or already verified (token-based)",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     title="Email Verified",
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Email verified successfully")
     *                 ),
     *                 @OA\Schema(
     *                     title="Already Verified",
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Email already verified")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to dashboard if already verified (session-based)",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect location",
     *             @OA\Schema(type="string", example="/dashboard?verified=1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse|JsonResponse
    {
        try {

            if ($request->user()->hasVerifiedEmail()) {
                // For token-based clients
                return $this->formatSuccessResponse(message: "Email already verified");
            }

            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }

            // For token-based clients
            return $this->formatSuccessResponse(message: "Email verified successfully");
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'email_verification');
        }
    }
}
