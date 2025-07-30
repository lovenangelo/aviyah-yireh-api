<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use ApiResponse;

    public function store(LoginRequest $request): Response|JsonResponse
    {
        $user = null;
        try {
            $request->authenticate();

            $user = Auth::user();
            $response = null;
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, "Two-factor Authentication");
        }

        if ($user->two_factor_enabled) {
            try {
                $user->generateTwoFactorCode();
                $user->sendTwoFactorCodeNotification();
                Auth::logout();
                $data = [
                    'two_factor_auth_required' => true,
                    'email' => $user->email
                ];
                $response = $this->formatSuccessResponse($data, "Two-factor authentication code has been sent to your email");
            } catch (\Exception $e) {
                Auth::login($user);

                $user->load('role');
                $token = $user->createToken('auth-token')->plainTextToken;

                $data = [
                    'user' => $user,
                    'token' => $token,
                    'warning' => 'Two-factor authentication is enabled but the code could not be sent. You have been logged in without 2FA verification.'
                ];
                $response = $this->handleApiException($e, $request, "Two-factor Authentication");
            }
        } else {
            $user->load('role');
            $token = $user->createToken('auth-token')->plainTextToken;
            $data = [
                'user' => $user,
                'token' => $token
            ];
            $response = $this->formatSuccessResponse($data, "Successfully logged in!");
        }

        return $response;
    }

    public function destroy(Request $request): Response|JsonResponse
    {
        // For token-based authentication, revoke the token
        $request->user()->currentAccessToken()->delete();
        return $this->formatSuccessResponse(message: "Token revoked successfully");
    }
}
