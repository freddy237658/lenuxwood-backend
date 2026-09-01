<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['admin', 'commercial','client']);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get(['id', 'name', 'email','phone','role', 'is_active']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'int', 'max:15'],
            'role' => ['required', 'in:admin,commercial,client'],
        ]);

        // Mot de passe temporaire ; à remplacer par un envoi d'email
        // d'invitation avec lien de définition de mot de passe.
        $temporaryPassword = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
        ]);

        return response()->json([
            'user' => $user->only('id', 'name', 'email','phone','role', 'is_active'),
            'temporary_password' => $temporaryPassword,
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,commercial,client'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update($validated);

        return $user->only('id', 'name', 'email', 'role', 'is_active');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé.',
        ]);
    }
}