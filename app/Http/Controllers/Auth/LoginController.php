<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && !$user->verified) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }

        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Logged in successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }
}
