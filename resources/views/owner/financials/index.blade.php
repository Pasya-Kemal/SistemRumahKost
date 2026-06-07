@extends('layouts.app')
@section('title', 'Rekap Keuangan')

@section('page-header')
    <h1 class="font-display text-3xl md:text-4xl">Rekap Pemasukan</h1>
    <p class="text-stone-400 mt-2">Pantau total pendapatan dari kamar yang tersewa di properti Anda.</p>
@endsection

@section('content')
{{-- Filter --}}
<form method="GET" action="{{ route('owner.financials.index') }}"
      class="mb-6 flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
    <div>
        <label class="text-xs uppercase tracking-widest text-stone-500">Bulan</label>
        <select name="month" data-testid="financial-month-filter"
                class="mt-1 bg-stone-900 border border-stone-800 rounded-md px-3 py-2 min-w-[150px]">
            <option value="">Semua</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                <option value="{{ $i+1 }}" @selected(request('month') == $i+1)>{{ $m }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs uppercase tracking-widest text-stone-500">Tahun</label>
        <input type="number" name="year" min="2020" max="2099" value="{{ request('year') }}" data-testid="financial-year-filter"
               placeholder="Semua"
               class="mt-1 bg-stone-900 border border-stone-800 rounded-md px-3 py-2 w-32">
    </div>
    <button type="submit" data-testid="financial-filter-submit"
        class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2.5 rounded-md transition">
        Terapkan
    </button>
    @if(request('month') || request('year'))
        <a href="{{ route('owner.financials.index') }}" class="text-stone-400 hover:text-stone-200 text-sm py-2.5">Reset</a>
    @endif
</form>

{{-- Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    @include('partials.stat-card', ['label' => 'Total Pemasukan', 'value' => 'Rp '.number_format($totalRevenue, 0, ',', '.'), 'icon' => 'trending-up', 'highlight' => true, 'tid' => 'financial-total'])
    @include('partials.stat-card', ['label' => 'Transaksi', 'value' => $payments->count(), 'icon' => 'receipt', 'tid' => 'financial-count'])
    @include('partials.stat-card', ['label' => 'Lunas', 'value' => $payments->where('status', 'paid')->count(), 'icon' => 'check-circle-2', 'tid' => 'financial-paid'])
</div>

{{-- Table --}}
@if($payments->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada transaksi pembayaran untuk periode terpilih.
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-stone-800">
        <table class="w-full text-sm">
            <thead class="bg-stone-900 text-stone-400">
                <tr>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    <th class="text-left px-4 py-3">Kamar / Kost</th>
                    <th class="text-left px-4 py-3">Penyewa</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    <tr data-testid="fin-row-{{ $p->id }}" class="border-t border-stone-800">
                        <td class="px-4 py-3">{{ optional($p->payment_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ optional(optional($p->booking)->room)->room_name }}</div>
                            <div class="text-xs text-stone-500">{{ optional(optional(optional($p->booking)->room)->kost)->kost_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-stone-400">{{ optional(optional($p->booking)->tenant)->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($p->status === 'paid')
                                <span class="px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">Lunas</span>
                            @else
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">{{ $p->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-amber-300">
                            Rp {{ number_format($p->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
