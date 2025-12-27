<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthController extends Controller
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::count() === 0 ? 'admin' : 'student',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Identifiants incorrects'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connecté avec succès',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function verifyFirebaseToken(Request $request)
    {
        $idTokenString = $request->input('token');

        if (!$idTokenString) {
            return response()->json(['error' => 'Token est requis'], 400);
        }

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($idTokenString);

            $claims = $verifiedIdToken->claims();
            $email = $claims->get('email');
            $name = $claims->get('name');

            $user = User::where('email', $email)->first();

            if (!$user) {
                $isFirstUser = User::count() === 0;
                $nameParts = explode(' ', $name, 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $user = User::create([
                    'name' => $name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'role' => $isFirstUser ? 'admin' : 'student',
                ]);
            }

            // Génération du token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Utilisateur connecté avec succès',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Token invalide ou expiré : ' . $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        // Supprime tous les tokens de l'utilisateur actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}