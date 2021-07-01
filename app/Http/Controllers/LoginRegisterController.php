<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginRegisterController extends Controller
{
    /**
     * Validate customer login
     */
    public function authenticate(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->only('email', 'password', 'session_token');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
            'session_token' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages(),
            ], 200);
        }

        try {
            $customer = User::where('email', $request['email'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login credentials are invalid.',
            ], 200);
        }

        try {
            if (! $customer || ! Hash::check($request['password'], $customer->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login credentials are invalid.',
            ], 200);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Customer has been logged in successfully.',
            'data' => $customer,
            'authenticated_data' => $token,
        ], 200);
    }


    public function logout(Request $request)
    {
        if (! $request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthoried request.',
            ], 200);
        }

        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer has been logged out',
            ], 200);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, customer cannot be logged out',
            ], 200);
        }
    }
}
