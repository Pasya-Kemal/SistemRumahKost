@extends('layouts.app')
@section('title', 'Tim Admin')

@section('page-header')
    <h1 class="font-display text-3xl md:text-4xl">Tim Admin</h1>
    <p class="text-stone-400 mt-2 max-w-2xl">
        Buat akun Admin untuk membantu mengelola kost Anda. Admin yang Anda tambahkan hanya bisa mengakses kamar di kost milik Anda.
    </p>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Admin form --}}
    <div class="lg:col-span-1">
        <div class="rounded-xl border border-stone-800 bg-ink-800 p-5">
            <h2 class="font-display text-xl">Tambah Admin Baru</h2>
            <p class="text-sm text-stone-400 mt-1">Admin baru akan menerima email & password yang Anda tetapkan.</p>

            <form method="POST" action="{{ route('owner.admins.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="text-sm text-stone-300">Nama Lengkap</label>
                    <input name="name" data-testid="admin-create-name" value="{{ old('name') }}" required minlength="2"
                           class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:border-amber-400 focus:outline-none" />
                    @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-stone-300">Email</label>
                    <input type="email" name="email" data-testid="admin-create-email" value="{{ old('email') }}" required
                           class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:border-amber-400 focus:outline-none" />
                    @error('email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-stone-300">Password</label>
                    <input type="password" name="password" data-testid="admin-create-password" required minlength="6"
                           class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:border-amber-400 focus:outline-none" />
                    @error('password') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-stone-300">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" data-testid="admin-create-password-confirm" required
                           class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:border-amber-400 focus:outline-none" />
                </div>

                <button type="submit" data-testid="admin-create-submit"
                        class="w-full bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold py-2.5 rounded-md transition">
                    Tambahkan ke Tim
                </button>
            </form>
        </div>
    </div>

    {{-- Admin list --}}
    <div class="lg:col-span-2">
        <div class="flex items-end justify-between mb-4">
            <h2 class="font-display text-xl">Daftar Admin ({{ $admins->count() }})</h2>
        </div>

        @if($admins->isEmpty())
            <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500"
                 data-testid="admin-list-empty">
                Belum ada Admin. Tambahkan satu lewat form di samping.
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-stone-800 bg-ink-800">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase tracking-wider text-stone-400 bg-stone-900/40">
                        <tr>
                            <th class="text-left px-5 py-3">Nama</th>
                            <th class="text-left px-5 py-3">Email</th>
                            <th class="text-left px-5 py-3">Bergabung</th>
                            <th class="text-right px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        <tr data-testid="admin-row-{{ $admin->id }}"
                            class="border-t border-stone-800/60 hover:bg-stone-900/40">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-400/10 text-amber-300 grid place-items-center text-xs font-semibold border border-amber-400/20">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $admin->name }}</div>
                                        <div class="text-[11px] text-stone-500">Admin Tim</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-stone-300">{{ $admin->email }}</td>
                            <td class="px-5 py-3 text-stone-400">{{ $admin->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('owner.admins.destroy', $admin) }}"
                                      onsubmit="return confirm('Hapus Admin {{ $admin->name }} dari tim Anda?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-testid="admin-delete-{{ $admin->id }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/10 rounded-md transition">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
