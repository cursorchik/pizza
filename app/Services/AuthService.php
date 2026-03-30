<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data) : array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        $token = Auth::login($user);

        return [
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ];
    }

    public function login(string $email, string $password): ?array
    {
        if (!$token = Auth::attempt(['email' => $email, 'password' => $password])) return null;

        return [
            'user' => Auth::user(),
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ];
    }

    public function logout() : void { Auth::logout(); }

    public function refresh() : array { return ['token' => Auth::refresh(), 'type'  => 'bearer']; }

    public function getCurrentUser(): ?Authenticatable { return Auth::user(); }
}
