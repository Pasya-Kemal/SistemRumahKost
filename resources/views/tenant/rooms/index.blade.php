@extends('layouts.app')
@section('title', 'Cari Kamar')

@section('page-header')
    <h1 class="font-display text-3xl md:text-4xl">Cari kamar kost</h1>
    <p class="text-stone-400 mt-2 max-w-2xl">Cari, bandingkan, dan booking kamar idaman dalam hitungan menit.</p>
@endsection

@section('content')
{{-- Search bar --}}
<form method="GET" action="{{ route('tenant.rooms.index') }}"
      class="mb-6 flex flex-col sm:flex-row gap-3"
      data-testid="tenant-search-form">
    <div class="relative flex-1">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-stone-500"></i>
        <input type="text" name="q" value="{{ $q ?? '' }}"
               data-testid="tenant-search-input"
               placeholder="Cari nama kamar, kost, alamat… (mis. Anggrek, Bandung)"
               class="w-full pl-9 pr-3 py-2.5 bg-stone-900 border border-stone-800 rounded-md text-stone-100 placeholder:text-stone-500 focus:outline-none focus:border-amber-400">
    </div>

    <div class="relative sm:w-56">
        <i data-lucide="map-pin" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-stone-500"></i>
        <input type="text" name="location" value="{{ $location ?? '' }}"
               data-testid="tenant-location-input"
               placeholder="Kota / lokasi"
               class="w-full pl-9 pr-3 py-2.5 bg-stone-900 border border-stone-800 rounded-md text-stone-100 placeholder:text-stone-500 focus:outline-none focus:border-amber-400">
    </div>

    <select name="gender" data-testid="tenant-gender-filter"
            class="bg-stone-900 border border-stone-800 rounded-md px-3 py-2.5 min-w-[140px]">
        <option value="">Semua Jenis</option>
        <option value="Pria"     @selected(request('gender') === 'Pria')>Pria</option>
        <option value="Wanita"   @selected(request('gender') === 'Wanita')>Wanita</option>
        <option value="Campuran" @selected(request('gender') === 'Campuran')>Campuran</option>
    </select>

    <button type="submit" data-testid="tenant-search-button"
            class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-5 py-2.5 rounded-md transition">
        Cari
    </button>
    @if(!empty($q) || request('gender') || !empty($location))
        <a href="{{ route('tenant.rooms.index') }}" data-testid="tenant-search-reset"
           class="inline-flex items-center justify-center px-4 py-2.5 text-stone-300 hover:text-stone-100 hover:bg-stone-800 rounded-md text-sm">
            Reset
        </a>
    @endif
</form>

{{-- Result count --}}
<div class="flex items-center justify-between mb-4 text-sm text-stone-400" data-testid="tenant-result-count">
    <div>
        Menampilkan <span class="text-stone-200">{{ $rooms->count() }}</span> dari
        <span class="text-stone-200">{{ $rooms->total() }}</span> kamar tersedia
        @if(!empty($q)) untuk <span class="text-amber-300">"{{ $q }}"</span> @endif
    </div>
    <div class="text-xs">Halaman {{ $rooms->currentPage() }} / {{ max(1, $rooms->lastPage()) }}</div>
</div>

@if($rooms->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Tidak ada kamar yang cocok. Coba kata kunci lain atau ubah filter.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($rooms as $room)
            <div data-testid="tenant-room-card-{{ $room->id }}"
                 class="rounded-xl border border-stone-800 bg-ink-800 hover:border-amber-400/40 transition flex flex-col overflow-hidden">

                {{-- Photo --}}
                <div class="aspect-[16/10] bg-stone-900 border-b border-stone-800 relative">
                    @php $primary = $room->photos->firstWhere('is_primary', true) ?? $room->photos->first(); @endphp
                    @if($primary)
                        <img src="{{ asset('storage/'.$primary->image_path) }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full grid place-items-center text-stone-700">
                            <i data-lucide="image" class="w-10 h-10"></i>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 px-2 py-0.5 text-[11px] font-medium rounded border border-amber-400/30 bg-amber-400/90 text-stone-900">
                        {{ optional($room->kost)->kost_gender }}
                    </span>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-semibold text-lg">{{ $room->room_name }}</h3>
                    <div class="text-sm text-stone-400 mt-0.5">{{ optional($room->kost)->kost_name }}</div>
                    <div class="text-xs text-stone-500 mt-2 flex items-start gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3 mt-0.5 shrink-0"></i>
                        <span class="line-clamp-2">{{ optional($room->kost)->address }}</span>
                    </div>
                    @if($room->description)
                        <p class="text-sm text-stone-400 mt-3 line-clamp-2">{{ $room->description }}</p>
                    @endif

                    <div class="mt-auto pt-5 flex items-end justify-between">
                        <div>
                            <div class="text-xs text-stone-500">Per bulan</div>
                            <div class="text-xl font-display text-amber-300">
                                Rp {{ number_format($room->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}"
                           data-testid="tenant-book-button-{{ $room->id }}"
                           class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2 rounded-md text-sm transition">
                            Booking
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8 pagination-wrap flex justify-center" data-testid="tenant-pagination">
        {{ $rooms->onEachSide(1)->links('pagination::tailwind') }}
    </div>
@endif
@endsection
