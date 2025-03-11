<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Create the credentials array.
        // We use 'name' as the username since the User model likely uses it as the identifier.
        $credentials = [
            'name'     => $request->input('username'),
            'password' => $request->input('password'),
        ];

        // Attempt to generate a token using the provided credentials.
        // We use the API guard configured for JWT.
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false, 
                'message' => 'Unauthorized'
            ], 401);
        }

        // On successful authentication, return the JWT with CORS headers.
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
        ]);
    }

    public function logout()
    {
        // Invalidate the JWT
        auth('api')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logged out'
        ]);
    }
}
