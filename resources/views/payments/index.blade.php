@extends('layouts.app')
@section('title', 'Pembayaran')

@section('page-header')
    <h1 class="font-display text-3xl md:text-4xl">Riwayat Pembayaran</h1>
    <p class="text-stone-400 mt-2">Semua transaksi pembayaran kamar Anda.</p>
@endsection

@section('content')
@if($payments->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada riwayat pembayaran.
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-stone-800">
        <table class="w-full text-sm">
            <thead class="bg-stone-900 text-stone-400">
                <tr>
                    <th class="text-left px-4 py-3">Referensi</th>
                    <th class="text-left px-4 py-3">Kamar / Kost</th>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    <th class="text-right px-4 py-3">Jumlah</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    @php $ref = data_get($p->payment_record, 'transaction_reference', 'TRX-'.substr($p->id, 0, 8)); @endphp
                    <tr data-testid="payment-row-{{ $p->id }}" class="border-t border-stone-800">
                        <td class="px-4 py-3 font-mono text-xs">{{ $ref }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ optional(optional($p->booking)->room)->room_name }}</div>
                            <div class="text-xs text-stone-500">{{ optional(optional(optional($p->booking)->room)->kost)->kost_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-stone-400">{{ optional($p->payment_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-amber-300">
                            Rp {{ number_format($p->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($p->status === 'paid')
                                <span class="px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Lunas</span>
                            @else
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400 capitalize">{{ $p->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('payments.show', $p) }}" class="text-amber-300 hover:underline text-xs">Detail →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
