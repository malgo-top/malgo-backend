<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractSignedMail;

class AuthController extends Controller
{

    public function register(Request $request){

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // $admin = Role::create(['name' => 'admin']);
        // $admin = Role::create(['name' => 'tenant']);
       //$user->assignRole('admin');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Role::firstOrCreate(['name' => 'tenant']);

        // $user->assignRole('admin');

        // $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json(['token' => $token]);

        return response()->json(['success' => true]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login', "success" => false], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "success" => true,
            "token" => $token,
            "user" => array_merge(
                $user->toArray(),
                ['role' => $user->getRoleNames()->first()]
            ),
        ]);

    }


    public function logout(Request $request){

        // Revoke current token only
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function changePassword(Request $request){

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true]);
    }


}
