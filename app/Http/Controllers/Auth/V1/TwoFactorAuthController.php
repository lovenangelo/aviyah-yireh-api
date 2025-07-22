<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\TwoFactorVerifyRequest;
use App\Http\Requests\Auth\TwoFactorToggleRequest;
use App\Traits\ApiResponse;

/**
 * @OA\Tag(
 *     name="Two-Factor Authentication",
 *     description="Endpoints for two-factor authentication (2FA) management and verification"
 * )
 */
class TwoFactorAuthController extends Controller
{
    use ApiResponse;
    /**
     * Toggle two-factor authentication for the authenticated user.
     *
     * @OA\Post(
     *     path="/api/v1/two-factor/toggle",
     *     summary="Enable or disable two-factor authentication",
     *     description="Toggles two-factor authentication for the authenticated user.",
     *     operationId="toggleTwoFactorAuth",
     *     tags={"Two-Factor Authentication"},
     *     security={
     *         {"bearer_token": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="2FA toggle data",
     *         @OA\JsonContent(
     *             required={"enabled"},
     *             @OA\Property(property="enabled", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="2FA toggled (token-based)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Two-factor authentication has been enabled."),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="2FA toggled (session-based, no content)"
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
    public function toggle(TwoFactorToggleRequest $request): JsonResponse|Response
    {
        $user = Auth::user();

        // Update the two_factor_enabled status
        $user->two_factor_enabled = $request->enabled;
        $user->save();

        // If enabling 2FA, reset any existing code
        if ($user->two_factor_enabled) {
            $user->resetTwoFactorCode();
        }

        $message = $user->two_factor_enabled
            ? 'Two-factor authentication has been enabled.'
            : 'Two-factor authentication has been disabled.';

        // Check if token-based authentication is used
        if ($request->expectsJson() || $request->hasHeader('X-Request-Token')) {
            return response()->json([
                'message' => $message,
                'user' => $user
            ]);
        }

        return response()->noContent();
    }

    /**
     * Verify the two-factor authentication code.
     *
     * @OA\Post(
     *     path="/api/v1/two-factor/verify",
     *     summary="Verify two-factor authentication code",
     *     description="Verifies the 2FA code for the user and logs them in.",
     *     operationId="verifyTwoFactorCode",
     *     tags={"Two-Factor Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="2FA verification data",
     *         @OA\JsonContent(
     *             required={"email", "code"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="2FA verified (token-based)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Two-factor authentication successful."),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string", example="1|abc123def456...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="2FA verified (session-based, no content)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired two-factor code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid or expired two-factor code.")
     *         )
     *     )
     * )
     */
    public function verify(TwoFactorVerifyRequest $request): JsonResponse|Response
    {
        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();

        $response = null;

        if (!$user) {
            $response = $this->formatErrorResponse(404, "User not found.");
        } elseif (!$user->verifyTwoFactorCode($request->code)) {
            $response = $this->formatErrorResponse(422, "Invalid or expired two-factor code.");
        } else {
            Auth::login($user);
            if ($request->expectsJson() || $request->hasHeader('X-Request-Token')) {
                $token = $user->createToken('auth-token')->plainTextToken;
                $user->load('role');
                $data = [
                    'user' => $user,
                    'token' => $token
                ];
                $response = $this->formatSuccessResponse($data, "Two-factor authentication successful.", 201, $request);
            } else {
                $request->session()->regenerate();
                $response = response()->noContent();
            }
        }

        return $response;
    }
}
