<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Os dados estão errados.',
                'errors' => [
                    'password' => [
                        'Credenciais inválidas'
                    ],
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $authToken,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();
        $user->driver = $driver;
        return $user;
    }
}
