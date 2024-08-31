<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // get user
        $user = User::where('email', $credentials['email'])->first();
        if(!$user) {
            return $this->sendError('Email has not been registered', null, 400);
        }

        // compare password
        if(!Hash::check($credentials['password'], $user->password)) {
            return $this->sendError('Email or password not match', null, 400);
        }

        $token = auth()->attempt($credentials);
        if(!$token) {
            return $this->sendError('Unauthorized', null, 401);
        }

        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ];

        return $this->sendResponse("Login successful", $response);
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'address' => $credentials['address'],
            'password' => Hash::make($credentials['password'])
        ]);

        return $this->sendResponse('User registered successfully');
    }

    public function logout()
    {
        auth()->invalidate(true);
        auth()->logout();
        return response()->json([
            'message' => 'Successfully log out'
        ], 200);
    }
}
