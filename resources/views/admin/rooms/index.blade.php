@extends('layouts.app')
@section('title', 'Kelola Kamar (Admin)')

@section('page-header')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl md:text-4xl">Kelola Kamar</h1>
            <p class="text-stone-400 mt-2">Akses penuh CRUD untuk seluruh kamar di semua kost.</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" data-testid="admin-add-room-button"
           class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2.5 rounded-md transition">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kamar
        </a>
    </div>
@endsection

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    @include('partials.stat-card', ['label' => 'Total Kamar', 'value' => $rooms->count(), 'icon' => 'door-open', 'tid' => 'admin-stat-rooms'])
    @include('partials.stat-card', ['label' => 'Kamar Tersedia', 'value' => $rooms->where('is_available', true)->count(), 'icon' => 'check-circle-2', 'highlight' => true, 'tid' => 'admin-stat-available'])
    @include('partials.stat-card', ['label' => 'Kamar Terisi', 'value' => $rooms->where('is_available', false)->count(), 'icon' => 'users', 'tid' => 'admin-stat-occupied'])
</div>

@if($rooms->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada kamar. Klik <span class="text-amber-300">Tambah Kamar</span> untuk menambahkan.
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-stone-800">
        <table class="w-full text-sm">
            <thead class="bg-stone-900 text-stone-400">
                <tr>
                    <th class="text-left px-4 py-3">Kamar</th>
                    <th class="text-left px-4 py-3">Kost</th>
                    <th class="text-left px-4 py-3">Deskripsi</th>
                    <th class="text-right px-4 py-3">Harga</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr data-testid="admin-room-row-{{ $room->id }}" class="border-t border-stone-800 hover:bg-stone-900/40">
                        <td class="px-4 py-3 font-medium">{{ $room->room_name }}</td>
                        <td class="px-4 py-3 text-stone-400">{{ optional($room->kost)->kost_name }}</td>
                        <td class="px-4 py-3 text-stone-400 max-w-xs truncate">{{ $room->description }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($room->is_available)
                                <span class="px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Tersedia</span>
                            @else
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">Terisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.rooms.show', $room) }}" data-testid="admin-show-room-{{ $room->id }}"
                                   class="p-2 rounded-md text-stone-300 hover:bg-stone-800">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.rooms.edit', $room) }}" data-testid="admin-edit-room-{{ $room->id }}"
                                   class="p-2 rounded-md text-stone-300 hover:bg-stone-800">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}"
                                      onsubmit="return confirm('Hapus kamar {{ $room->room_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-testid="admin-delete-room-{{ $room->id }}"
                                        class="p-2 rounded-md text-red-400 hover:bg-red-500/10">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
