<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $data['username'];

        $user = User::where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $role = Role::firstOrCreate(['role_name' => 'tenants']);

        $user = User::create([
            'role_id' => $role->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Tenant::create([
            'user_id' => $user->id,
            'phone' => '0000000000',
            'gender' => 'unknown',
            'address' => 'Belum diisi',
            'emergency_contact' => '0000000000',
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('role');

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    private function formatUser(User $user)
    {
        $roleName = $user->role?->role_name ?? 'tenants';
        $label = match ($roleName) {
            'admin' => 'Admin',
            'owner' => 'Owner',
            default => 'Tenant',
        };

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'initials' => $this->initials($user->name),
            'role' => [
                'key' => $roleName,
                'label' => $label,
            ],
        ];
    }

    private function initials(string $name)
    {
        $parts = preg_split('/\s+/', trim($name));
        $first = strtoupper(substr($parts[0] ?? '', 0, 1));
        $second = strtoupper(substr($parts[1] ?? '', 0, 1));
        return $first . $second;
    }
}
