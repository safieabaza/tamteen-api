<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    // =========================
    // SEND OTP
    // =========================
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $recentOtp = Otp::where('email', $request->email)
            ->where('created_at', '>', now()->subSeconds(60))
            ->first();

        if ($recentOtp) {
            return response()->json([
                'message' => 'Please wait 60 seconds before requesting another OTP'
            ], 429);
        }

        Otp::where('email', $request->email)->delete();

        $code = rand(100000, 999999);

        Otp::create([
            'email' => $request->email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5)
        ]);

        Mail::to($request->email)->send(new SendOtpMail($code));

        return response()->json([
            'message' => 'OTP sent to email successfully'
        ]);
    }

    // =========================
    // VERIFY OTP
    // =========================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($request->code, $otp->code)) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            ['is_verified' => true]
        );

        $token = JWTAuth::fromUser($user);

        $otp->delete();

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // =========================
    // UPDATE PROFILE
    // =========================
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}