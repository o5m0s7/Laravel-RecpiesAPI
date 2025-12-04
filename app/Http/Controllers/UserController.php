<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'status' => 'success',
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json([
                'message' => 'Invalid login details',
                'status' => 'error'
            ], 401);

        $user = User::where('email', $data['email'])->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $currentToken = $request->user()->currentAccessToken();
            if ($currentToken) {
                $request->user()->tokens()->where('id', $currentToken->id)->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Logged out']);
        }

        return response()->json(['status' => 'error', 'message' => 'Not authenticated'], 401);
    }
}
