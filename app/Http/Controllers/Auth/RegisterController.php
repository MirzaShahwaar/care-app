<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',                        // Minimum 8 characters
                'regex:/[a-z]/',                 // At least one lowercase letter
                'regex:/[A-Z]/',                 // At least one uppercase letter
                'regex:/[0-9]/',                 // At least one number
            ],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        return response()->json(['message' => 'Registration successful. Please check your email for the OTP.']);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verified' => false,
        ]);

        $this->sendOtp($user);

        return $user;
    }

    protected function sendOtp(User $user)
    {
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::raw('Your OTP code is: ' . $otp, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your OTP Code');
        });
    }
}
