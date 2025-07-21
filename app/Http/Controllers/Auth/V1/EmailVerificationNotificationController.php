<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints for email verification and notifications"
 * )
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @OA\Post(
     *     path="/api/v1/email/verification-notification",
     *     summary="Send a new email verification notification",
     *     description="Sends a new email verification link to the authenticated user. Handles both token-based and session-based requests.",
     *     operationId="sendEmailVerificationNotification",
     *     tags={"Email Verification"},
     *     security={
     *         {"bearer_token": {}}
     *     },
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based authentication request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification link sent or already verified",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     title="Verification Link Sent",
     *                     type="object",
     *                     @OA\Property(property="status", type="string", example="verification-link-sent")
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
     *             @OA\Schema(type="string", example="/dashboard")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests (throttled)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Too many requests. Please try again later.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    }
}
