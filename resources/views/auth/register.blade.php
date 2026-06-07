@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="-mt-10 -mx-6 min-h-[calc(100vh-0px)] grid md:grid-cols-2">
    <div class="relative hidden md:flex flex-col justify-between p-12 bg-gradient-to-br from-ink-700 via-ink-800 to-ink-900 border-r border-stone-800/60">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-400 text-stone-900 grid place-items-center font-bold text-xl">K</div>
            <div>
                <div class="font-display text-2xl">KostNest</div>
                <div class="text-xs uppercase tracking-[0.2em] text-stone-400">Sistem Manajemen Rumah Kost</div>
            </div>
        </div>
        <div>
            <h2 class="font-display text-4xl leading-snug">
                Gabung sekarang,<br>
                <span class="text-amber-400">mulai cari atau kelola kost.</span>
            </h2>
            <p class="text-stone-400 mt-4 max-w-md">
                Pilih peranmu di KostNest. Tenant untuk menyewa kamar, Owner untuk mengelola kost dan keuangan secara mandiri.
            </p>
        </div>
        <div class="text-xs text-stone-500">Sudah punya akun? <a href="{{ url('/login') }}" class="text-amber-300 hover:underline">Masuk di sini</a></div>
    </div>

    <div class="flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="flex border border-stone-800 rounded-lg overflow-hidden bg-ink-800 mb-6">
                <a href="{{ url('/login') }}" class="flex-1 text-center py-2 text-sm font-medium text-stone-300 hover:bg-stone-900">Masuk</a>
                <a href="{{ url('/register') }}" class="flex-1 text-center py-2 text-sm font-medium bg-amber-400 text-stone-900">Daftar</a>
            </div>

            <h3 class="font-display text-2xl" data-testid="register-heading">Daftar sebagai {{ $roles[$selectedRole] }}</h3>
            <p class="text-sm text-stone-400 mt-1">
                @switch($selectedRole)
                    @case('admin')   Akun Admin memiliki akses penuh untuk mengelola seluruh kost di platform. @break
                    @case('owner')   Akun Owner dapat menambah kost, mengelola kamar, dan melihat rekap keuangan. @break
                    @default         Akun Tenant dapat mencari kamar dan melakukan pemesanan.
                @endswitch
            </p>

            <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-4">
                @csrf

                {{-- Role chooser --}}
                <div>
                    <label class="text-sm text-stone-300">Pilih Peran</label>
                    <div class="mt-2 grid grid-cols-3 gap-2" data-testid="register-role-group">
                        @foreach($roles as $key => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="{{ $key }}"
                                       data-testid="register-role-{{ $key }}"
                                       @checked(old('role', $selectedRole) === $key)
                                       class="peer sr-only" />
                                <span class="block text-center px-3 py-2 rounded-md border border-stone-800 bg-stone-900 text-stone-300 text-sm peer-checked:border-amber-400 peer-checked:bg-amber-400/10 peer-checked:text-amber-300 hover:border-stone-700 transition">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('role') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-stone-300">Nama Lengkap</label>
                    <input name="name" data-testid="register-name" value="{{ old('name') }}" required minlength="2"
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-stone-300">Email</label>
                    <input type="email" name="email" data-testid="register-email" value="{{ old('email') }}" required
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-stone-300">Password</label>
                    <input type="password" name="password" data-testid="register-password" required minlength="6"
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-stone-300">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" data-testid="register-password-confirm" required
                        class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2" />
                </div>
                <button type="submit" data-testid="register-submit"
                    class="w-full bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold py-2.5 rounded-md transition">
                    Daftar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
