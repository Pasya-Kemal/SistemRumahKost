@extends('layouts.app')
@section('title', 'Bayar Booking')

@section('page-header')
    <a href="{{ route('bookings.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke booking
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Bayar Booking</h1>
    <p class="text-stone-400 mt-2">Konfirmasi pembayaran untuk kamar yang Anda pesan.</p>
@endsection

@section('content')
@php $total = optional($booking->room)->price * $booking->duration_months; @endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <form method="POST" action="{{ route('payments.store') }}" class="md:col-span-2 rounded-xl border border-stone-800 bg-ink-800 p-6 md:p-8 space-y-5">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

        <div class="rounded-md bg-stone-900 border border-stone-800 p-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-stone-400">Kamar</span><span>{{ optional($booking->room)->room_name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Kost</span><span>{{ optional(optional($booking->room)->kost)->kost_name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Periode</span><span>{{ optional($booking->start_date)->format('d M Y') }} · {{ $booking->duration_months }} bulan</span></div>
            <div class="border-t border-stone-800 pt-2 flex justify-between">
                <span class="text-stone-400">Total</span>
                <span class="text-amber-300 font-semibold text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div>
            <label class="text-sm text-stone-300">Metode Pembayaran</label>
            <select name="payment_method" data-testid="payment-method"
                    class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
                <option value="manual_transfer">Transfer Manual</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="e_wallet">E-Wallet</option>
                <option value="cash">Tunai</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-stone-300">Tanggal Pembayaran</label>
            <input type="date" name="payment_date" data-testid="payment-date" required
                   value="{{ old('payment_date', now()->toDateString()) }}"
                   class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
        </div>

        <input type="hidden" name="amount" value="{{ $total }}">

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" data-testid="payment-submit"
                class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-5 py-2.5 rounded-md transition">
                Konfirmasi Pembayaran
            </button>
            <a href="{{ route('bookings.index') }}" class="text-stone-400 hover:text-stone-200 text-sm">Batal</a>
        </div>
    </form>

    <div class="rounded-xl border border-stone-800 bg-ink-800 p-6 h-fit space-y-3">
        <div class="text-xs uppercase tracking-widest text-stone-500">Catatan</div>
        <p class="text-sm text-stone-400 leading-relaxed">
            Setelah pembayaran dikonfirmasi, status booking akan otomatis berubah menjadi <span class="text-emerald-300 font-medium">Aktif</span>.
            Bukti pembayaran dapat Anda lihat di halaman riwayat.
        </p>
    </div>
</div>
@endsection
