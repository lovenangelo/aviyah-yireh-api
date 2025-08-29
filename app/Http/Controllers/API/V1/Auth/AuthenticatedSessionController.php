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
            $user->load('role');
            $token = $user->createToken('auth-token')->plainTextToken;
            $data = [
                'user' => $user,
                'token' => $token,
            ];

            return $this->formatSuccessResponse($data, 'Successfully logged in!');
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Two-factor Authentication');
        }
    }

    public function destroy(Request $request): Response|JsonResponse
    {
        // For token-based authentication, revoke the token
        $request->user()->currentAccessToken()->delete();

        return $this->formatSuccessResponse(message: 'Token revoked successfully');
    }
}
