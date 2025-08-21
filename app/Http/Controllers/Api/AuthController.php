<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user
    */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Login a user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // If authentication is successful, get the authenticated user
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Send reset password link
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Use Laravel's built-in password broker to send the reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Check if the status is successful
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 422);
    }

    /**
     * Reset the user's password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Use Laravel's built-in password broker to reset the password
        $status = Password::reset(
            // Get the request data
            $request->only('email', 'password', 'password_confirmation', 'token'),
            // Closure to update the user's password
            function (User $user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        // Check if the status is successful
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        // If there was an error, return an error response
        return response()->json(['message' => __($status)], 400);
    }
}
