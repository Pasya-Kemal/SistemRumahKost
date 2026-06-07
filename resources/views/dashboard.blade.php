@extends('layouts.app')
@section('title', 'Dashboard')

@php
    /** @var \App\Models\User $user */
    /** @var string $role  one of: admin | owner | tenant */
    $rupiah = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
@endphp

{{-- =========================================================
     OWNER DASHBOARD — KPI + recent bookings
     ========================================================= --}}
@if($role === 'owner')

    @section('page-header')
        <h1 class="font-display text-3xl md:text-4xl">Halo, {{ $user->name }} 👋</h1>
        <p class="text-stone-400 mt-2 max-w-2xl">
            Berikut ringkasan informasi kost anda. Gunakan bar navigasi untuk kelola operasional kost dan keuangan.
        </p>
    @endsection

    @section('content')
        {{-- KPI strip --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @include('partials.stat-card', [
                'icon'  => 'building-2',
                'label' => 'Total Kost',
                'value' => $stats['kosts'],
                'tid'   => 'kpi-kosts',
            ])
            @include('partials.stat-card', [
                'icon'  => 'door-open',
                'label' => 'Total Kamar',
                'value' => $stats['rooms'],
                'tid'   => 'kpi-rooms',
            ])
            @include('partials.stat-card', [
                'icon'  => 'check-circle-2',
                'label' => 'Kamar Tersedia',
                'value' => $stats['available'],
                'tid'   => 'kpi-available',
            ])
            @include('partials.stat-card', [
                'icon'  => 'trending-up',
                'label' => 'Pemasukan Bulan Ini',
                'value' => $rupiah($stats['revenueMonth']),
                'tid'   => 'kpi-revenue',
            ])
        </div>

        {{-- Recent bookings --}}
        <section class="mt-10" data-testid="owner-recent-bookings">
            <div class="flex items-end justify-between mb-4">
                <h2 class="font-display text-2xl">Booking terbaru</h2>
                <a href="{{ route('rooms.index') }}" class="text-sm text-amber-300 hover:underline">
                    Lihat semua kamar →
                </a>
            </div>

            @if($recentBookings->isEmpty())
                <div class="p-10 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
                    Belum ada booking masuk. Pastikan kamar Anda berstatus
                    <span class="text-amber-300">Tersedia</span> agar dapat dipesan tenant.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-stone-800 bg-ink-800">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase tracking-wider text-stone-400">
                            <tr class="border-b border-stone-800">
                                <th class="text-left px-5 py-3">Kamar / Kost</th>
                                <th class="text-left px-5 py-3">Penyewa</th>
                                <th class="text-left px-5 py-3">Mulai</th>
                                <th class="text-left px-5 py-3">Durasi</th>
                                <th class="text-left px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentBookings as $b)
                            <tr class="border-b border-stone-800/60 last:border-0 hover:bg-stone-900/40">
                                <td class="px-5 py-3">
                                    <div class="font-medium">{{ $b->room->room_name ?? '-' }}</div>
                                    <div class="text-xs text-stone-500">{{ optional($b->room->kost)->kost_name }}</div>
                                </td>
                                <td class="px-5 py-3">{{ optional($b->tenant)->name ?? '-' }}</td>
                                <td class="px-5 py-3">{{ optional($b->start_date)->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ $b->duration_months }} bulan</td>
                                <td class="px-5 py-3">
                                    @php
                                        $color = [
                                            'active'    => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                            'pending'   => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                            'completed' => 'bg-stone-500/15 text-stone-300 border-stone-500/30',
                                        ][$b->status] ?? 'bg-stone-500/15 text-stone-300 border-stone-500/30';
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 text-[11px] font-medium rounded border {{ $color }}">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endsection

{{-- =========================================================
     ADMIN DASHBOARD — KPI + recent rooms
     ========================================================= --}}
@elseif($role === 'admin')

    @section('page-header')
        <h1 class="font-display text-3xl md:text-4xl">Manajemen Kamar</h1>
        <p class="text-stone-400 mt-2 max-w-2xl">
            Hak akses penuh untuk mengelola semua kamar di setiap cabang kost.
        </p>
    @endsection

    @section('content')
        {{-- KPI strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @include('partials.stat-card', [
                'icon'  => 'building-2',
                'label' => 'Total Kost',
                'value' => $stats['kosts'],
                'tid'   => 'kpi-kosts',
            ])
            @include('partials.stat-card', [
                'icon'  => 'door-open',
                'label' => 'Total Kamar',
                'value' => $stats['rooms'],
                'tid'   => 'kpi-rooms',
            ])
            @include('partials.stat-card', [
                'icon'  => 'user-check',
                'label' => 'Kamar Terisi',
                'value' => $stats['occupied'],
                'tid'   => 'kpi-occupied',
            ])
        </div>

        {{-- Recently added rooms --}}
        <section class="mt-10" data-testid="admin-recent-rooms">
            <div class="flex items-end justify-between mb-4">
                <h2 class="font-display text-2xl">Kamar terbaru ditambahkan</h2>
                <a href="{{ route('admin.rooms.index') }}" class="text-sm text-amber-300 hover:underline">
                    Kelola semua kamar →
                </a>
            </div>

            @if($recentRooms->isEmpty())
                <div class="p-10 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
                    Belum ada kamar terdaftar.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-stone-800 bg-ink-800">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase tracking-wider text-stone-400">
                            <tr class="border-b border-stone-800">
                                <th class="text-left px-5 py-3">Kamar</th>
                                <th class="text-left px-5 py-3">Kost</th>
                                <th class="text-left px-5 py-3">Alamat</th>
                                <th class="text-right px-5 py-3">Harga</th>
                                <th class="text-left px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentRooms as $r)
                            <tr class="border-b border-stone-800/60 last:border-0 hover:bg-stone-900/40">
                                <td class="px-5 py-3 font-medium">{{ $r->room_name }}</td>
                                <td class="px-5 py-3">{{ optional($r->kost)->kost_name }}</td>
                                <td class="px-5 py-3 text-stone-400">{{ optional($r->kost)->address }}</td>
                                <td class="px-5 py-3 text-right font-mono text-amber-300">{{ $rupiah($r->price) }}</td>
                                <td class="px-5 py-3">
                                    @if($r->is_available)
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-medium rounded border bg-emerald-500/15 text-emerald-300 border-emerald-500/30">Tersedia</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-medium rounded border bg-stone-500/15 text-stone-300 border-stone-500/30">Terisi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endsection

{{-- =========================================================
     TENANT DASHBOARD — Mamikos-style hero + cards
     ========================================================= --}}
@else

    {{-- Skip the default page-header padding so the hero can go edge-to-edge inside the main container --}}
    @section('content')
        {{-- Hero with search --}}
        <section class="relative overflow-hidden rounded-2xl border border-stone-800 bg-gradient-to-br from-amber-500/10 via-ink-800 to-ink-900 px-6 md:px-10 py-12 md:py-16"
                 data-testid="tenant-hero">
            <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-amber-400/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-amber-300/5 blur-3xl"></div>

            <div class="relative max-w-3xl">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] uppercase tracking-[0.18em] bg-amber-400/15 text-amber-300 border border-amber-400/30">
                    <i data-lucide="sparkles" class="w-3 h-3"></i> Selamat datang, {{ $user->name }}
                </span>
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl mt-4 leading-tight">
                    Temukan <span class="text-amber-300">kost idamanmu</span><br class="hidden md:block">
                    dalam hitungan menit.
                </h1>
                <p class="text-stone-300 mt-4 max-w-xl">
                    Dapatkan infonya dan langsung sewa di KostNest.
                </p>

                {{-- Search form --}}
                <form method="GET" action="{{ route('tenant.rooms.index') }}"
                      class="mt-7 grid grid-cols-1 md:grid-cols-12 gap-2 bg-ink-900/80 backdrop-blur border border-stone-800 rounded-xl p-2"
                      data-testid="tenant-hero-search">
                    <div class="relative md:col-span-5">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-stone-500"></i>
                        <input type="text" name="q"
                               data-testid="hero-search-q"
                               placeholder="Masukan nama lokasi/area/alamat"
                               class="w-full pl-9 pr-3 py-3 bg-transparent rounded-md text-stone-100 placeholder:text-stone-500 focus:outline-none">
                    </div>
                    <div class="relative md:col-span-4 md:border-l md:border-stone-800">
                        <i data-lucide="map-pin" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-stone-500"></i>
                        <input type="text" name="location"
                               data-testid="hero-search-location"
                               placeholder="Kota (mis. Bandung)"
                               class="w-full pl-9 pr-3 py-3 bg-transparent rounded-md text-stone-100 placeholder:text-stone-500 focus:outline-none">
                    </div>
                    <select name="gender" data-testid="hero-search-gender"
                            class="md:col-span-2 bg-transparent border-l border-stone-800 px-3 py-3 text-stone-200 focus:outline-none">
                        <option value="" class="bg-ink-900">Semua</option>
                        <option value="Pria" class="bg-ink-900">Pria</option>
                        <option value="Wanita" class="bg-ink-900">Wanita</option>
                        <option value="Campuran" class="bg-ink-900">Campuran</option>
                    </select>
                    <button type="submit" data-testid="hero-search-submit"
                            class="md:col-span-1 bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold rounded-md py-3 transition">
                        Cari
                    </button>
                </form>

                {{-- Popular city chips --}}
                @if($popularCities->isNotEmpty())
                    <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                        <span class="text-stone-500">Populer:</span>
                        @foreach($popularCities as $city)
                            <a href="{{ route('tenant.rooms.index', ['location' => $city]) }}"
                               data-testid="chip-city-{{ Str::slug($city) }}"
                               class="px-3 py-1 rounded-full border border-stone-800 hover:border-amber-400/50 hover:text-amber-300 text-stone-300 transition">
                                {{ $city }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Active booking banner --}}
        @if($activeBooking)
            <section class="mt-8 rounded-xl border border-amber-400/30 bg-amber-400/5 px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3"
                     data-testid="tenant-active-booking">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-400/20 text-amber-300 grid place-items-center shrink-0">
                        <i data-lucide="bookmark-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider text-amber-300">Booking aktif kamu</div>
                        <div class="font-semibold mt-0.5">
                            {{ $activeBooking->room->room_name }} di
                            <span class="text-stone-300 font-normal">{{ optional($activeBooking->room->kost)->kost_name }}</span>
                        </div>
                        <div class="text-xs text-stone-400 mt-1">
                            Mulai {{ optional($activeBooking->start_date)->format('d M Y') }}, durasi {{ $activeBooking->duration_months }} bulan, status
                            <span class="text-amber-300 capitalize">{{ $activeBooking->status }}</span>.
                        </div>
                    </div>
                </div>
                <a href="{{ route('bookings.show', $activeBooking) }}"
                   data-testid="active-booking-link"
                   class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2 rounded-md text-sm transition shrink-0">
                    Lihat detail →
                </a>
            </section>
        @endif

        {{-- Available rooms grid with pagination --}}
        <section class="mt-12" data-testid="tenant-available-rooms">
            <div class="flex items-end justify-between mb-5">
                <div>
                    <h2 class="font-display text-2xl md:text-3xl">Daftar kost tersedia</h2>
                    <p class="text-stone-400 text-sm mt-1">Pilih kamar yang cocok untukmu, langsung klik untuk booking.</p>
                </div>
                <a href="{{ route('tenant.rooms.index') }}" class="text-sm text-amber-300 hover:underline whitespace-nowrap">
                    Cari lebih spesifik →
                </a>
            </div>

            @if($availableRooms->isEmpty())
                <div class="p-10 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
                    Belum ada kamar yang tersedia saat ini.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableRooms as $room)
                        <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}"
                           data-testid="reco-room-{{ $room->id }}"
                           class="group rounded-xl border border-stone-800 bg-ink-800 hover:border-amber-400/40 transition flex flex-col overflow-hidden">

                            <div class="aspect-[16/10] bg-stone-900 relative">
                                @php $primary = $room->photos->firstWhere('is_primary', true) ?? $room->photos->first(); @endphp
                                @if($primary)
                                    <img src="{{ asset('storage/'.$primary->image_path) }}" alt="" class="w-full h-full object-cover group-hover:scale-[1.02] transition">
                                @else
                                    <div class="w-full h-full grid place-items-center text-stone-700">
                                        <i data-lucide="bed-double" class="w-12 h-12"></i>
                                    </div>
                                @endif
                                <span class="absolute top-3 left-3 px-2 py-0.5 text-[11px] font-medium rounded bg-amber-400 text-stone-900">
                                    {{ optional($room->kost)->kost_gender }}
                                </span>
                                <span class="absolute top-3 right-3 px-2 py-0.5 text-[11px] font-semibold rounded bg-ink-900/80 backdrop-blur text-amber-300 border border-amber-400/30">
                                    Tersedia
                                </span>
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-semibold text-lg group-hover:text-amber-300 transition">{{ $room->room_name }}</h3>
                                <div class="text-sm text-stone-400 mt-0.5">{{ optional($room->kost)->kost_name }}</div>
                                <div class="text-xs text-stone-500 mt-2 flex items-start gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 mt-0.5 shrink-0"></i>
                                    <span class="line-clamp-1">{{ optional($room->kost)->address }}</span>
                                </div>

                                <div class="mt-auto pt-5 flex items-end justify-between">
                                    <div>
                                        <div class="text-xs text-stone-500">Mulai dari</div>
                                        <div class="text-lg font-display text-amber-300">{{ $rupiah($room->price) }}<span class="text-xs text-stone-500 font-sans"> / bln</span></div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-amber-300 text-sm group-hover:translate-x-0.5 transition">
                                        Booking <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center" data-testid="tenant-dashboard-pagination">
                    {{ $availableRooms->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif
        </section>
    @endsection

@endif
