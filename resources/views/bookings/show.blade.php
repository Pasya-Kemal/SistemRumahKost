@extends('layouts.app')
@section('title', 'Detail Booking')

@section('page-header')
    <a href="{{ route('bookings.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Detail Booking</h1>
@endsection

@section('content')
@php $total = optional($booking->room)->price * $booking->duration_months; @endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2 rounded-xl border border-stone-800 bg-ink-800 p-6 space-y-4">
        <div>
            <div class="text-xs uppercase tracking-widest text-stone-500">Kamar</div>
            <div class="font-display text-2xl mt-1">{{ optional($booking->room)->room_name }}</div>
            <div class="text-sm text-stone-400">{{ optional(optional($booking->room)->kost)->kost_name }}</div>
            <div class="text-xs text-stone-500 mt-1 flex items-start gap-1">
                <i data-lucide="map-pin" class="w-3 h-3 mt-0.5 shrink-0"></i>
                {{ optional(optional($booking->room)->kost)->address }}
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-stone-800">
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Mulai</div>
                <div class="mt-1">{{ optional($booking->start_date)->format('d M Y') }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Durasi</div>
                <div class="mt-1">{{ $booking->duration_months }} bulan</div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-stone-800 bg-ink-800 p-6 h-fit space-y-3">
        <div class="text-xs uppercase tracking-widest text-stone-500">Total Tagihan</div>
        <div class="font-display text-3xl text-amber-300">Rp {{ number_format($total, 0, ',', '.') }}</div>
        <div class="pt-3 border-t border-stone-800">
            <div class="text-xs uppercase tracking-widest text-stone-500">Status</div>
            <div class="mt-1 capitalize">{{ $booking->status }}</div>
        </div>
        @if($booking->status === 'pending')
            <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}"
               class="block text-center bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2.5 rounded-md transition mt-2">
                Konfirmasi Pembayaran
            </a>
        @endif
    </div>
</div>
@endsection
