@extends('layouts.app')
@section('title', 'Daftar Kamar')

@section('page-header')
    <h1 class="font-display text-3xl md:text-4xl">Daftar Kamar</h1>
    <p class="text-stone-400 mt-2">Lihat status seluruh kamar di properti kost Anda.</p>
@endsection

@section('content')
@if($rooms->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada kamar di kost Anda.
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-stone-800">
        <table class="w-full text-sm">
            <thead class="bg-stone-900 text-stone-400">
                <tr>
                    <th class="text-left px-4 py-3">Kamar</th>
                    <th class="text-left px-4 py-3">Kost</th>
                    <th class="text-left px-4 py-3">Deskripsi</th>
                    <th class="text-right px-4 py-3">Harga / Bulan</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr data-testid="owner-room-row-{{ $room->id }}" class="border-t border-stone-800 hover:bg-stone-900/40">
                        <td class="px-4 py-3 font-medium">{{ $room->room_name }}</td>
                        <td class="px-4 py-3 text-stone-400">{{ optional($room->kost)->kost_name }}</td>
                        <td class="px-4 py-3 text-stone-400 max-w-md truncate">{{ $room->description }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($room->is_available)
                                <span class="px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Tersedia</span>
                            @else
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">Terisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('rooms.show', $room) }}" class="text-amber-300 hover:underline text-xs">Detail →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
