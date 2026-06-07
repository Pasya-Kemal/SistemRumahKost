@extends('layouts.app')
@section('title', 'Detail Pembayaran')

@section('page-header')
    <a href="{{ route('payments.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Detail Pembayaran</h1>
@endsection

@section('content')
@php
    $rec = $payment->payment_record ?? [];
    $ref = data_get($rec, 'transaction_reference', 'TRX-'.substr($payment->id, 0, 8));
    $tenant = data_get($rec, 'snapshots.tenant.name');
    $kost = data_get($rec, 'snapshots.property.kost_name');
    $roomName = data_get($rec, 'snapshots.property.room_name');
    $perMonth = data_get($rec, 'snapshots.property.price_per_month');
    $months = data_get($rec, 'financial_recap.duration_months', $payment->booking->duration_months ?? 1);
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2 rounded-xl border border-stone-800 bg-ink-800 p-6 space-y-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Referensi</div>
                <div class="font-mono mt-1">{{ $ref }}</div>
            </div>
            <span class="px-3 py-1 rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-xs">
                @if($payment->status === 'paid') Lunas @else {{ ucfirst($payment->status) }} @endif
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-stone-800">
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Tanggal</div>
                <div class="mt-1">{{ optional($payment->payment_date)->format('d M Y') }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Metode</div>
                <div class="mt-1 capitalize">{{ str_replace('_', ' ', data_get($rec, 'payment_channel', '—')) }}</div>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-800 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-stone-400">Penyewa</span><span>{{ $tenant ?? auth()->user()->name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Kamar</span><span>{{ $roomName ?? optional(optional($payment->booking)->room)->room_name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Kost</span><span>{{ $kost ?? optional(optional(optional($payment->booking)->room)->kost)->kost_name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Harga / bulan</span><span>Rp {{ number_format($perMonth ?? optional(optional($payment->booking)->room)->price ?? 0, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-stone-400">Durasi</span><span>{{ $months }} bulan</span></div>
        </div>
    </div>

    <div class="rounded-xl border border-stone-800 bg-ink-800 p-6 h-fit space-y-3">
        <div class="text-xs uppercase tracking-widest text-stone-500">Total Dibayar</div>
        <div class="font-display text-3xl text-amber-300">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
    </div>
</div>
@endsection
