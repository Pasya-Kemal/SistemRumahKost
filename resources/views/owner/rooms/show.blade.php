@extends('layouts.app')
@section('title', $room->room_name)

@section('page-header')
    <a href="{{ route('rooms.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
    </a>
    <h1 class="font-display text-3xl md:text-4xl">{{ $room->room_name }}</h1>
    <p class="text-stone-400 mt-2">{{ optional($room->kost)->kost_name }} · {{ optional($room->kost)->address }}</p>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2 rounded-xl border border-stone-800 bg-ink-800 p-6">
        <h2 class="font-display text-xl mb-3">Deskripsi</h2>
        <p class="text-stone-300 leading-relaxed whitespace-pre-line">{{ $room->description ?: '—' }}</p>

        @if($room->photos && $room->photos->count())
            <h3 class="font-display text-lg mt-8 mb-3">Foto</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($room->photos as $photo)
                    <div class="aspect-video bg-stone-900 rounded-lg overflow-hidden border border-stone-800">
                        <img src="{{ asset('storage/'.$photo->image_path) }}" alt="" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-stone-800 bg-ink-800 p-6 h-fit space-y-4">
        <div>
            <div class="text-xs uppercase tracking-widest text-stone-500">Harga / Bulan</div>
            <div class="font-display text-3xl text-amber-300 mt-1">Rp {{ number_format($room->price, 0, ',', '.') }}</div>
        </div>
        <div class="pt-3 border-t border-stone-800">
            <div class="text-xs uppercase tracking-widest text-stone-500">Status</div>
            @if($room->is_available)
                <span class="mt-1 inline-flex px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Tersedia</span>
            @else
                <span class="mt-1 inline-flex px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">Terisi</span>
            @endif
        </div>
    </div>
</div>
@endsection
