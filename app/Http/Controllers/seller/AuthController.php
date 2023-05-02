<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
        ]);

        $seller = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => 'seller'
        ]);

        if($seller) {
            return response()->json([
                'msg' => 'Register Success',
                'data' => $seller
            ]);
        }

        return response()->json([
            'msg' => 'Internal Server Error'
        ]);

    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        $email = $request->email;
        $password = $request->password;
        $account = User::where('email', $email)->first();

        if (! $account || ! Hash::check($password, $account->password) || $account->level != 'seller') {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $account->createToken($email)->plainTextToken;

        return response()->json([
            "msg" => "Login Success",
            "token" => $token,
            "account" => $account
        ]);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "msg" => "Logout Success"
        ]);
    }
}
