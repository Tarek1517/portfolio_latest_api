<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class SanctumAuthController extends Controller
{
    /**
     * Current authenticated user
     */
    public function me(Request $request)
    {
        if ($request->user()->tokenCan('admin')) {
            return new AdminResource($request->user());
        } elseif ($request->user()->tokenCan('user')) {
            return new UserResource($request->user());
        } else {
            return response()->json([
                'message' => 'Unauthorized Access'
            ], 401);
        }
    }

    // Admin Login
    public function adminLogin(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        return response()->json([
            'access_token' => $admin->createToken('admin', ['admin'])->plainTextToken,
            'loggedIn' => true,
            'user' => new AdminResource($admin)
        ]);
    }

    // Student Login
    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $student = User::where('email', $request->email)->first();
        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided phone or password incorrect.'],
            ]);
        }

        $student->tokens()->delete();

        return response()->json([
            'access_token' => $student->createToken('user', ['user'])->plainTextToken,
            'loggedIn' => true,
            'user' => new UserResource($student)
        ]);
    }

    // Student Register
    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|phone|unique:users,phone',
            'password' => 'required',
            'confirmPassword' => 'required|same:password',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'access_token' => $user->createToken('user', ['user'])->plainTextToken,
            'loggedIn' => true,
            'user' => new UserResource($user)
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(15),
            ]
        );

        Mail::to($user->email)->send(new ResetPasswordOtpMail($user->name, $otp));

        return response()->json(['message' => 'OTP sent successfully'], 200);
    }

    public function checkOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->otp !== (string) $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'OTP Checked'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'nullable',
            'new_password_confirmation' => 'nullable|same:new_password',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    // User Logout
    public function userLogout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
            'loggedIn' => false,
        ], 200);
    }
}
