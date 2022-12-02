<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {

        $request->validate([
            "login"    => "required",
            "password" => "required",
        ]);

        if (!$token = auth()->attempt([
            'login'     => $request->login,
            'password'  => $request->password,
            'is_active' => true,
        ])) {
            return $this->jsonUnathorized();
        }

        return $this->jsonSuccess($this->getJsonJwtData($token));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {

        auth()->logout();

        return $this->jsonSuccess();
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(UserRepository::getAuthenticatedUser());
    }

    /**
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        return response()->json(auth('api')->refresh());
    }

    /**
     * @param string $token
     *
     * @return array
     */
    private function getJsonJwtData(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL(),
            'user'         => UserRepository::getAuthenticatedUser(),
        ];
    }
}
