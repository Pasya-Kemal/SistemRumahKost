@extends('layouts.app')
@section('title', $kost->kost_name)

@section('page-header')
    <a href="{{ route('kosts.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
    </a>
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl md:text-4xl">{{ $kost->kost_name }}</h1>
            <p class="text-stone-400 mt-2 flex items-start gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 mt-1 shrink-0"></i>
                <span>{{ $kost->address }}</span>
            </p>
            <span class="inline-flex mt-3 px-2 py-0.5 text-[11px] font-medium rounded border border-amber-400/30 bg-amber-400/10 text-amber-300">
                {{ $kost->kost_gender }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kosts.edit', $kost) }}"
               class="inline-flex items-center gap-2 border border-stone-800 hover:border-amber-400/40 text-stone-200 px-4 py-2.5 rounded-md">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit
            </a>
        </div>
    </div>
@endsection

@section('content')
<h2 class="font-display text-xl mb-4">Daftar Kamar</h2>

@if($kost->rooms->isEmpty())
    <div class="p-10 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada kamar di kost ini. Admin akan menambahkan kamar ke dalam kost Anda.
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-stone-800">
        <table class="w-full text-sm">
            <thead class="bg-stone-900 text-stone-400">
                <tr>
                    <th class="text-left px-4 py-3">Kamar</th>
                    <th class="text-left px-4 py-3">Deskripsi</th>
                    <th class="text-right px-4 py-3">Harga</th>
                    <th class="text-center px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kost->rooms as $room)
                    <tr class="border-t border-stone-800 hover:bg-stone-900/40">
                        <td class="px-4 py-3 font-medium">{{ $room->room_name }}</td>
                        <td class="px-4 py-3 text-stone-400 max-w-md truncate">{{ $room->description }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($room->is_available)
                                <span class="px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Tersedia</span>
                            @else
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">Terisi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
