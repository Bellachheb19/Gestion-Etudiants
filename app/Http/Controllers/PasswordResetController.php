<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // Générer un nouveau mot de passe aléatoire
        $newPassword = Str::random(10);

        // Mettre à jour le mot de passe de l'utilisateur
        $user->password = Hash::make($newPassword);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\NewPasswordMail($newPassword));
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi de mail : " . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Un nouveau mot de passe a été envoyé à votre adresse email.',
            'debug_password' => $newPassword // Optionnel, à retirer en production
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'Token invalide ou expiré.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.']);
    }
}
