@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="-mt-10 -mx-6 min-h-[calc(100vh-0px)] grid md:grid-cols-2">
    {{-- Left visual panel --}}
    <div class="relative hidden md:flex flex-col justify-between p-12 bg-gradient-to-br from-ink-700 via-ink-800 to-ink-900 border-r border-stone-800/60 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" style="background:
            radial-gradient(circle at 20% 20%, rgba(251,191,36,0.08), transparent 40%),
            radial-gradient(circle at 80% 80%, rgba(251,191,36,0.04), transparent 50%);"></div>
        <div class="relative flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-400 text-stone-900 grid place-items-center font-bold text-xl">K</div>
            <div>
                <div class="font-display text-2xl">KostNest</div>
                <div class="text-xs uppercase tracking-[0.2em] text-stone-400">Sistem Manajemen Rumah Kost</div>
            </div>
        </div>

        <div class="relative">
            <h2 class="font-display text-4xl leading-snug">
                Kelola kost dengan
                <span class="text-amber-400">tenang.</span>
            </h2>
            <p class="text-stone-400 mt-4 max-w-md">
                Satu tempat untuk pemilik, admin, dan penyewa. Cari kamar, kelola booking, dan rekap keuangan secara rapi di satu dashboard.
            </p>

            <div class="mt-10 grid grid-cols-3 gap-3 max-w-md">
                @foreach([
                    ['role' => 'Admin',  'email' => 'admin@kostnest.id',  'pass' => 'admin123'],
                    ['role' => 'Owner',  'email' => 'owner@kostnest.id',  'pass' => 'owner123'],
                    ['role' => 'Tenant', 'email' => 'tenant@kostnest.id', 'pass' => 'tenant123'],
                ] as $s)
                <button type="button"
                    data-testid="quickfill-{{ strtolower($s['role']) }}"
                    onclick="document.getElementById('login-username').value='{{ $s['email'] }}'; document.getElementById('login-password').value='{{ $s['pass'] }}';"
                    class="text-left p-3 rounded-lg border border-stone-800 bg-stone-900/40 hover:border-amber-400/60 hover:bg-stone-900 transition">
                    <div class="text-[10px] uppercase tracking-widest text-stone-500">Coba sebagai</div>
                    <div class="font-semibold text-amber-300 mt-1">{{ $s['role'] }}</div>
                    <div class="text-[11px] text-stone-500 mt-1 truncate">{{ $s['email'] }}</div>
                </button>
                @endforeach
            </div>
        </div>

        <div class="relative text-xs text-stone-500 flex items-center gap-2">
            <i data-lucide="building-2" class="w-4 h-4"></i> Kelola Kost, Kamar, Booking, dan Pembayaran dalam satu aplikasi.
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="flex border border-stone-800 rounded-lg overflow-hidden bg-ink-800 mb-6">
                <a href="{{ url('/login') }}" data-testid="tab-login"
                   class="flex-1 text-center py-2 text-sm font-medium bg-amber-400 text-stone-900">Masuk</a>
                <a href="{{ url('/register') }}" data-testid="tab-register"
                   class="flex-1 text-center py-2 text-sm font-medium text-stone-300 hover:bg-stone-900">Daftar</a>
            </div>

            <h3 class="font-display text-2xl">Masuk ke akun Anda</h3>
            <p class="text-sm text-stone-400 mt-1">Gunakan email atau nama pengguna terdaftar.</p>

            <form method="POST" action="{{ url('/login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-sm text-stone-300">Email / Username</label>
                    <input id="login-username" name="username" data-testid="login-username"
                        value="{{ old('username') }}" required
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 text-stone-100 focus:outline-none focus:border-amber-400" />
                </div>
                <div>
                    <label class="text-sm text-stone-300">Password</label>
                    <input id="login-password" type="password" name="password" data-testid="login-password" required
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 text-stone-100 focus:outline-none focus:border-amber-400" />
                </div>
                <button type="submit" data-testid="login-submit"
                    class="w-full bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold py-2.5 rounded-md transition">
                    Masuk
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-stone-800/60">
                <p class="text-xs text-stone-500 text-center">Belum punya akun? Daftar sebagai:</p>
                <div class="mt-3 grid grid-cols-3 gap-2">
                    @foreach($roles as $key => $label)
                        <a href="{{ url('/register?role='.$key) }}"
                           data-testid="register-as-{{ $key }}"
                           class="text-center px-3 py-2 rounded-md border border-stone-800 hover:border-amber-400/60 hover:text-amber-300 text-stone-300 text-sm transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
