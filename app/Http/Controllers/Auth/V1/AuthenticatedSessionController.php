<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints for login and logout"
 * )
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Authenticate user and handle 2FA if enabled",
     *     description="Authenticates a user with email and password. Supports both session-based and token-based authentication. If two-factor authentication is enabled, sends a 2FA code via email.",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User's email address",
     *                 example="user@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="User's password",
     *                 example="password123"
     *             ),
     *             @OA\Property(
     *                 property="remember",
     *                 type="boolean",
     *                 description="Remember the user session",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="device_name",
     *                 type="string",
     *                 description="Device name for token-based authentication",
     *                 example="Mobile App"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based authentication request",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication or 2FA required",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     title="Successful Login with Token",
     *                     type="object",
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="user@example.com"),
     *                         @OA\Property(property="phone", type="string", example="+1234567890"),
     *                         @OA\Property(property="role_id", type="integer", example=1),
     *                         @OA\Property(property="two_factor_enabled", type="boolean", example=false),
     *                         @OA\Property(property="avatar_url", type="string", nullable=true),
     *                         @OA\Property(property="email_verified_at", type="string", format="date-time"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(
     *                             property="role",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="admin")
     *                         )
     *                     ),
     *                     @OA\Property(property="token", type="string", example="1|abc123def456...")
     *                 ),
     *                 @OA\Schema(
     *                     title="Two-Factor Authentication Required",
     *                     type="object",
     *                     @OA\Property(property="two_factor_auth_required", type="boolean", example=true),
     *                     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                     @OA\Property(property="message", type="string", example="Two-factor authentication code has been sent to your email.")
     *                 ),
     *                 @OA\Schema(
     *                     title="Login with 2FA Warning",
     *                     type="object",
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="user@example.com")
     *                     ),
     *                     @OA\Property(property="token", type="string", example="1|abc123def456..."),
     *                     @OA\Property(property="warning", type="string", example="Two-factor authentication is enabled but the code could not be sent. You have been logged in without 2FA verification.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Login successful for session-based authentication (no JSON response)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="These credentials do not match our records."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="These credentials do not match our records.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="Too many login attempts. Please try again in 60 seconds.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(LoginRequest $request): Response|JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $response = null;

        if ($user->two_factor_enabled) {
            try {
                $user->generateTwoFactorCode();
                $user->sendTwoFactorCodeNotification();
                Auth::logout();

                $response = $request->isTokenRequest()
                    ? response()->json([
                        'message' => 'Two-factor authentication code has been sent to your email.',
                        'two_factor_auth_required' => true,
                        'email' => $user->email
                    ], 200)
                    : response()->json([
                        'two_factor_auth_required' => true,
                        'email' => $user->email
                    ], 200);
            } catch (\Exception $e) {
                Auth::login($user);

                if ($request->isTokenRequest()) {
                    $user->load('role');
                    $token = $user->createToken('auth-token')->plainTextToken;

                    $response = response()->json([
                        'user' => $user,
                        'token' => $token,
                        'warning' => 'Two-factor authentication is enabled but the code could not be sent. You have been logged in without 2FA verification.'
                    ]);
                } else {
                    $request->session()->regenerate();
                    $response = response()->noContent();
                }
            }
        } else {
            if ($request->isTokenRequest()) {
                $user->load('role');
                $token = $user->createToken('auth-token')->plainTextToken;

                $response = response()->json([
                    'user' => $user,
                    'token' => $token
                ]);
            } else {
                $request->session()->regenerate();
                $response = response()->noContent();
            }
        }

        return $response;
    }

    /**
     * Destroy an authenticated session.
     *
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout user and destroy session or revoke token",
     *     description="Logs out the authenticated user. For token-based authentication, revokes the current access token. For session-based authentication, destroys the session.",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={
     *         {"bearer_token": {}}
     *     },
     *     @OA\Parameter(
     *         name="X-Request-Token",
     *         in="header",
     *         description="Header to indicate token-based authentication",
     *         required=false,
     *         @OA\Schema(type="string", example="true")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token revoked successfully (for token-based auth)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token revoked successfully"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Session destroyed successfully (for session-based auth)"
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
    public function destroy(Request $request): Response|JsonResponse
    {
        // For token-based authentication, revoke the token
        if ($request->hasHeader('X-Request-Token') && $request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Token revoked successfully',
                'status' => 'success'
            ]);
        }

        // For session-based authentication
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
