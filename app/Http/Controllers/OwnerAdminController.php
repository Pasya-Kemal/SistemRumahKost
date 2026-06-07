<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Tim Admin: an Owner can add/remove Admin accounts that are scoped to
 * the Owner's kosts. Each Admin created here has `created_by_owner_id`
 * set to the current owner's id.
 */
class OwnerAdminController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function (Request $request, Closure $next) {
                $role = optional($request->user()?->role)->role_name;
                abort_unless($role === 'owner', 403, 'Hanya Owner yang dapat mengakses Tim Admin.');
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $admins = User::with('role')
            ->where('created_by_owner_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('owner.admins.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'min:2'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);

        User::create([
            'role_id'             => $adminRole->id,
            'created_by_owner_id' => Auth::id(),
            'name'                => $data['name'],
            'email'               => $data['email'],
            'password'            => Hash::make($data['password']),
        ]);

        return redirect()->route('owner.admins.index')
            ->with('success', 'Akun Admin berhasil ditambahkan ke tim Anda.');
    }

    public function destroy(User $admin)
    {
        abort_unless((int) $admin->created_by_owner_id === (int) Auth::id(), 403,
            'Anda hanya dapat menghapus Admin yang Anda buat.');

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('owner.admins.index')
            ->with('success', "Admin {$name} berhasil dihapus dari tim Anda.");
    }
}
