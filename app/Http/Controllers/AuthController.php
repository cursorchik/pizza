<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    public function register(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:11',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);

        return $this->success([
            'user'          => $user,
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ], 'User registered successfully');
    }

    public function login(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->success([
            'user'          => Auth::user(),
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ], 'Login successful');

//        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return $this->success(Auth::user());
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return $this->success(null, 'Successfully logged out');
    }

    public function refresh(): JsonResponse
    {
        return $this->success([
            'token' => auth()->refresh(),
            'type'  => 'bearer',
        ], 'Token refreshed');
    }

//    protected function respondWithToken(string $token) : JsonResponse
//    {
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => 'bearer',
//            'expires_in' => auth()->factory()->getTTL() * 60
//        ]);
//    }
}
