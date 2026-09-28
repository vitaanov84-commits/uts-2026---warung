<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Email atau password tidak sesuai.'], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'photo' => $user->photo,
                'photo_url' => $user->photo ? asset('storage/' . ltrim($user->photo, '/')) : null,
            ],
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $path = $request->file('photo')->store('photos', 'public');
        $user->update(['photo' => $path]);

        return response()->json([
            'message' => 'Foto profil berhasil ditambahkan',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'photo_url' => $user->photo ? asset('storage/' . ltrim($user->photo, '/')) : null,
            ],
        ]);
    }
}
