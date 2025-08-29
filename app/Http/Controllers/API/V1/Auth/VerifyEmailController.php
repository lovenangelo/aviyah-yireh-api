<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Email verification hash",
     *
     *         @OA\Schema(type="string", example="abcdef123456")
     *     ),
     *
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based verification request",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="true")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email verified or already verified (token-based)",
     *
     *         @OA\JsonContent(
     *             oneOf={
     *
     *                 @OA\Schema(
     *                     title="Email Verified",
     *                     type="object",
     *
     *                     @OA\Property(property="message", type="string", example="Email verified successfully")
     *                 ),
     *
     *                 @OA\Schema(
     *                     title="Already Verified",
     *                     type="object",
     *
     *                     @OA\Property(property="message", type="string", example="Email already verified")
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to dashboard if already verified (session-based)",
     *
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect location",
     *
     *             @OA\Schema(type="string", example="/dashboard?verified=1")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $result = $this->formatSuccessResponse(message: 'Email verified successfully');
            // Check if email is already verified
            if ($request->user()->hasVerifiedEmail()) {
                return $this->formatSuccessResponse(message: 'Email already verified');
            }

            // Validate the request - ensure code is provided
            $request->validate([
                'code' => 'required|string|size:6',
            ]);

            // Get the verification code from the request
            $code = $request->input('code');

            // Verify the email verification code using the User model method
            if (! $request->user()->verifyEmailVerificationCode($code)) {
                $result = $this->formatErrorResponse(
                    code: 'INVALID_VERIFICATION_CODE',
                    message: 'Invalid or expired verification code',
                    statusCode: 422
                );
            } else {
                // Mark email as verified
                $request->user()->markEmailAsVerified();
            }

            return $result;
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'email_verification');
        }
    }
}
