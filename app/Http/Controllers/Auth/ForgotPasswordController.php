<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Send an OTP to the user's email for password reset.
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        $otp = rand(1000, 9999);

        PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        Mail::raw('Your OTP code for password reset is: ' . $otp, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
            'password' => [
                'required',
                'string',
                'min:8',                        // Minimum 8 characters
                'regex:/[a-z]/',                 // At least one lowercase letter
                'regex:/[A-Z]/',                 // At least one uppercase letter
                'regex:/[0-9]/',                 // At least one number
                'confirmed',                     // Password confirmation
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $passwordReset = PasswordReset::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successful.']);
    }
}
