<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validate->errors(),
            ], 422);
        }

        $isEmail = filter_var($request->email, FILTER_VALIDATE_EMAIL);

        // Authentication logic here
        $fields = [
            $isEmail ? 'email' : 'username' => $request->email,
            'password' => $request->password,
        ];

        // Attempt to authenticate the user
        if (!Auth::attempt($fields)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::where('id', Auth::id())->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 200);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }
}
