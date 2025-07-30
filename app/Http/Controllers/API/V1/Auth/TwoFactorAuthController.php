<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    use ApiResponse;

    public function toggle(Request $request): JsonResponse|Response
    {
        try {
            $user = Auth::user();

            request()->validate([
                'enabled' => 'required|boolean',
            ]);

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

            return $this->formatSuccessResponse(null, $message, 201, $request);
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to toggle two-factor authentication.');
        }
    }

    public function verify(Request $request): JsonResponse|Response
    {
        try {
            request()->validate([
                'email' => 'required|email',
                'code' => 'required|numeric|digits:6',
            ]);
            $email = $request->email;
            $user = \App\Models\User::where('email', $email)->first();

            $response = null;

            if (!$user) {
                $response = $this->formatErrorResponse(code: "USER_NOT_FOUND", message: "User not found.", statusCode: 404);
            } elseif (!$user->verifyTwoFactorCode($request->code)) {
                $response = $this->formatErrorResponse(code: "INVALID_OR_EXPIRED_CODE", message: "Invalid or expired two-factor code.", statusCode: 422);
            } else {
                $token = $user->createToken('auth-token')->plainTextToken;
                $user->load('role');
                $data = [
                    'user' => $user,
                    'token' => $token
                ];
                $response = $this->formatSuccessResponse($data, "Two-factor authentication successful.", 201, $request);
            }
            return $response;
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to verify two-factor authentication code.');
        }
    }
}
