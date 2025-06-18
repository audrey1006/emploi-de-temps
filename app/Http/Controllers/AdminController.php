<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    private function checkAdmin(Request $request)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            throw ValidationException::withMessages([
                'role' => ['Accès non autorisé. Vous devez être administrateur.']
            ]);
        }
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
        ];

        return response()->json([
            'message' => 'Tableau de bord admin',
            'stats' => $stats
        ]);
    }

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json([
            'users' => $users
        ]);
    }

    public function activateUser(User $user)
    {
        $user->update(['is_active' => true]);
        return response()->json([
            'message' => 'Utilisateur activé avec succès',
            'user' => $user
        ]);
    }

    public function deactivateUser(User $user)
    {
        $user->update(['is_active' => false]);
        return response()->json([
            'message' => 'Utilisateur désactivé avec succès',
            'user' => $user
        ]);
    }

    public function registerTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'enseignant',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Enseignant créé avec succès',
            'teacher' => $teacher
        ], 201);
    }

    public function createTeacher(Request $request)
    {
        $this->checkAdmin($request);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'required|string|max:20'
            ]);

            $teacher = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'teacher',
                'is_active' => true
            ]);

            return response()->json([
                'message' => 'Enseignant créé avec succès',
                'teacher' => $teacher
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTeachers(Request $request)
    {
        $this->checkAdmin($request);

        $teachers = User::where('role', 'teacher')->get();
        return response()->json([
            'teachers' => $teachers
        ]);
    }

    public function updateTeacher(Request $request, $id)
    {
        $this->checkAdmin($request);

        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'phone' => 'sometimes|string|max:20'
            ]);

            if ($request->has('name')) {
                $teacher->name = $request->name;
            }
            if ($request->has('email')) {
                $teacher->email = $request->email;
            }
            if ($request->has('password')) {
                $teacher->password = Hash::make($request->password);
            }
            if ($request->has('phone')) {
                $teacher->phone = $request->phone;
            }

            $teacher->save();

            return response()->json([
                'message' => 'Enseignant mis à jour avec succès',
                'teacher' => $teacher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTeacher(Request $request, $id)
    {
        $this->checkAdmin($request);

        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);
            $teacher->delete();

            return response()->json([
                'message' => 'Enseignant supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}