@extends('layouts.app')
@section('title', 'Booking Kamar')

@section('page-header')
    <a href="{{ route('tenant.rooms.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke pencarian
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Booking · {{ $room->room_name }}</h1>
    <p class="text-stone-400 mt-2">{{ optional($room->kost)->kost_name }} · {{ optional($room->kost)->address }}</p>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <form method="POST" action="{{ route('bookings.store') }}" class="md:col-span-2 rounded-xl border border-stone-800 bg-ink-800 p-6 md:p-8 space-y-5">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">

        <div>
            <label class="text-sm text-stone-300">Tanggal Mulai Sewa</label>
            <input type="date" name="start_date" data-testid="booking-start-date" required
                   min="{{ now()->toDateString() }}"
                   value="{{ old('start_date', now()->toDateString()) }}"
                   class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
        </div>
        <div>
            <label class="text-sm text-stone-300">Durasi (bulan)</label>
            <input type="number" name="duration_months" min="1" max="12" data-testid="booking-duration" required
                   value="{{ old('duration_months', 1) }}"
                   class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" data-testid="booking-submit"
                class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-5 py-2.5 rounded-md transition">
                Konfirmasi Booking
            </button>
            <a href="{{ route('tenant.rooms.index') }}" class="text-stone-400 hover:text-stone-200 text-sm">Batal</a>
        </div>
    </form>

    <div class="rounded-xl border border-stone-800 bg-ink-800 p-6 h-fit space-y-3">
        <div class="text-xs uppercase tracking-widest text-stone-500">Ringkasan</div>
        <div class="flex justify-between text-sm">
            <span class="text-stone-400">Kamar</span><span>{{ $room->room_name }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-stone-400">Kost</span><span>{{ optional($room->kost)->kost_name }}</span>
        </div>
        <div class="flex justify-between text-sm pb-3 border-b border-stone-800">
            <span class="text-stone-400">Harga / bulan</span>
            <span class="text-amber-300 font-semibold">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
        </div>
        <p class="text-xs text-stone-500">Total tagihan akan dihitung otomatis berdasar durasi yang Anda pilih.</p>
    </div>
</div>
@endsection
