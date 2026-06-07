<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Session-based web auth used by the Blade UI.
 * The API-token flow remains in AuthController.
 */
class WebAuthController extends Controller
{
    /** Public-facing role keys available for self-registration → display label */
    private const ROLE_LABELS = [
        'owner'  => 'Owner',
        'tenant' => 'Tenant',
    ];

    /** Public role key → role_name stored in DB */
    private const ROLE_DB_NAME = [
        'owner'  => 'owner',
        'tenant' => 'tenants',
    ];

    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login', ['roles' => self::ROLE_LABELS]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $data['username'];

        $user = User::with('role')
            ->where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Email/username atau password salah.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) return redirect()->route('dashboard');

        $selected = $request->query('role', 'tenant');
        if (!array_key_exists($selected, self::ROLE_LABELS)) {
            $selected = 'tenant';
        }

        return view('auth.register', [
            'roles'        => self::ROLE_LABELS,
            'selectedRole' => $selected,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys(self::ROLE_LABELS))],
        ]);

        $dbRoleName = self::ROLE_DB_NAME[$data['role']];
        $role       = Role::firstOrCreate(['role_name' => $dbRoleName]);

        $user = User::create([
            'role_id'  => $role->id,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Tenant profile is only relevant when registering as a tenant.
        if ($data['role'] === 'tenant') {
            Tenant::create([
                'user_id'           => $user->id,
                'phone'             => '0000000000',
                'gender'            => 'unknown',
                'address'           => 'Belum diisi',
                'emergency_contact' => '0000000000',
                'status'            => 'active',
                'joined_at'         => now()->toDateString(),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di KostNest!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}
