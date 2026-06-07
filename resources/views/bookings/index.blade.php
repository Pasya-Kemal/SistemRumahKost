@extends('layouts.app')
@section('title', 'Booking Saya')

@section('page-header')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl md:text-4xl">Booking Saya</h1>
            <p class="text-stone-400 mt-2">Pantau status booking & lakukan pembayaran.</p>
        </div>
        <a href="{{ route('tenant.rooms.index') }}"
           class="inline-flex items-center gap-2 border border-stone-800 hover:border-amber-400/40 text-stone-200 px-4 py-2.5 rounded-md">
            <i data-lucide="search" class="w-4 h-4"></i> Cari Kamar Lain
        </a>
    </div>
@endsection

@section('content')
@if($bookings->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Anda belum punya booking. <a href="{{ route('tenant.rooms.index') }}" class="text-amber-300 hover:underline">Cari kamar dulu →</a>
    </div>
@else
    <div class="space-y-3">
        @foreach($bookings as $b)
            <div data-testid="booking-card-{{ $b->id }}"
                 class="rounded-xl border border-stone-800 bg-ink-800 p-5 flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-lg">{{ optional($b->room)->room_name }}</h3>
                        @switch($b->status)
                            @case('pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded border border-amber-400/30 bg-amber-400/10 text-amber-300">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                </span>
                                @break
                            @case('active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-300">
                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> Aktif
                                </span>
                                @break
                            @default
                                <span class="px-2 py-0.5 text-[11px] rounded border border-stone-700 bg-stone-700/40 text-stone-400">{{ $b->status }}</span>
                        @endswitch
                    </div>
                    <div class="text-sm text-stone-400">
                        {{ optional(optional($b->room)->kost)->kost_name }} · {{ optional(optional($b->room)->kost)->address }}
                    </div>
                    <div class="text-xs text-stone-500 mt-2 flex flex-wrap items-center gap-4">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ optional($b->start_date)->format('d M Y') }} · {{ $b->duration_months }} bulan
                        </span>
                        <span>
                            Total:
                            <span class="text-amber-300 font-semibold">
                                Rp {{ number_format(optional($b->room)->price * $b->duration_months, 0, ',', '.') }}
                            </span>
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('bookings.show', $b) }}"
                       class="text-stone-300 hover:text-stone-100 text-sm px-3 py-2 rounded-md hover:bg-stone-800">Detail</a>
                    @if($b->status === 'pending')
                        <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}"
                           data-testid="pay-button-{{ $b->id }}"
                           class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2 rounded-md text-sm transition">
                            Bayar
                        </a>
                        <form method="POST" action="{{ route('bookings.destroy', $b) }}"
                              onsubmit="return confirm('Batalkan booking ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" data-testid="cancel-booking-{{ $b->id }}"
                                class="text-red-400 hover:bg-red-500/10 px-3 py-2 rounded-md text-sm">
                                Batal
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if(method_exists($bookings, 'links'))
        <div class="mt-8 pagination-wrap flex justify-center">{{ $bookings->links('pagination::tailwind') }}</div>
    @endif
@endif
@endsection
