<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Traits\ApiResponses;
use App\Services\AuthService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponses;

    protected AuthService $authService;

    public function __construct(AuthService $authService) { $this->authService = $authService; }

    public function register(RegisterRequest  $request) : JsonResponse
    {
        $result = $this->authService->register($request->only(['name', 'email', 'phone', 'password']));
        return $this->success($result, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $result = $this->authService->login($request->email, $request->password);
        if (!$result) return $this->error('Unauthorized', 401);

        return $this->success($result, 'Login successful');
    }

    public function me() : JsonResponse { return $this->success($this->authService->getCurrentUser()); }

    public function logout() : JsonResponse
    {
        $this->authService->logout();
        return $this->success(null, 'Successfully logged out');
    }

    public function refresh() : JsonResponse
    {
        $result = $this->authService->refresh();
        return $this->success($result, 'Token refreshed');
    }
}
