<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // L'admin voit tous les utilisateurs ayant le rôle 'student'
            return response()->json(User::where('role', 'student')->get(), 200);
        }

        // Un étudiant ne voit que ses propres données
        return response()->json([$user], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $student = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt(Str::random(16)),
            'role' => 'student',
            'phone' => $request->phone,
        ]);

        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = User::where('role', 'student')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        return response()->json($student, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = User::find($id);
        $user = $request->user();

        if (!$student) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Vérification des permissions : admin ou soi-même
        if ($user->role !== 'admin' && $user->id !== $student->id) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        if ($request->has('first_name') && $request->has('last_name')) {
            $data['name'] = $request->first_name . ' ' . $request->last_name;
        }

        $student->update($data);

        return response()->json($student, 200);
    }

    public function uploadPhoto(Request $request, string $id)
    {
        $user = User::find($id);
        $currentUser = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Seul l'admin ou l'utilisateur lui-même peuvent changer la photo
        if ($currentUser->role !== 'admin' && $currentUser->id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/profiles'), $fileName);

            $user->photo = 'assets/profiles/' . $fileName;
            $user->save();

            return response()->json([
                'message' => 'Photo mise à jour avec succès',
                'photo' => $user->photo
            ], 200);
        }

        return response()->json(['message' => 'Aucun fichier reçu'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $student = User::find($id);
        $user = $request->user();

        if (!$student) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $student->delete();

        return response()->json(['message' => 'Étudiant supprimé avec succès'], 200);
    }
}
