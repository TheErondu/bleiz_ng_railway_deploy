<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseApiController
{
    /**
     * User login API.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            $response = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'email_verified' => $user->hasVerifiedEmail(),
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            activity()->causedBy($user)->log('User logged in via API');

            return $this->sendResponse($response, 'Login successful');
        } else {
            return $this->sendError('Invalid credentials', [], 401);
        }
    }

    /**
     * User registration API.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
        ]);

        $user->assignRole('customer');
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'email_verified' => false,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];

        activity()->causedBy($user)->log('User registered via API');

        return $this->sendResponse($response, 'Registration successful', 201);
    }

    /**
     * User logout API.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        activity()->causedBy($user)->log('User logged out via API');

        return $this->sendResponse([], 'Logout successful');
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'roles' => $user->getRoleNames(),
            'email_verified' => $user->hasVerifiedEmail(),
            'created_at' => $user->created_at,
        ];

        // Add role-specific data
        if ($user->hasRole('customer')) {
            $customer = $user->customer;
            if ($customer) {
                $userData['customer_profile'] = [
                    'address' => $customer->address,
                    'employer' => $customer->employer,
                    'bank_name' => $customer->bank_name,
                ];
            }
        }

        return $this->sendResponse($userData, 'User data retrieved successfully');
    }

    /**
     * Forgot password API.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Send password reset link
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->sendResponse([], 'Password reset link sent successfully');
        } else {
            return $this->sendError('Unable to send password reset link');
        }
    }
}
