<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends BaseApiController
{
    /**
     * Handle authentication login request.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->sendError('As credenciais fornecidas estão incorretas.', [], 401);
        }

        if (!$user->is_active) {
            return $this->sendError('Esta conta de utilizador está inativa.', [], 403);
        }

        $deviceName = $validated['device_name'] ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Autenticação realizada com sucesso.');
    }

    /**
     * Revoke current API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Sessão encerrada com sucesso.');
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->sendResponse(new UserResource($request->user()), 'Dados do perfil recuperados.');
    }
}
