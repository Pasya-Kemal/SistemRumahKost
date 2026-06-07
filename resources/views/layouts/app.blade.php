<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KostNest') · Sistem Rumah Kost</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Manrope', 'ui-sans-serif', 'system-ui'],
              display: ['"DM Serif Display"', 'ui-serif', 'Georgia'],
            },
            colors: {
              ink: {
                900: '#0f1115',
                800: '#13161c',
                700: '#1c1f27',
                600: '#262932',
              }
            }
          }
        }
      }
    </script>

    <style>
        html, body { background: #0f1115; color: #e7e5e4; font-family: 'Manrope', sans-serif; }
        .font-display { font-family: 'DM Serif Display', serif; letter-spacing: -0.01em; }
        ::selection { background: #fbbf24; color: #1c1917; }

        /* Pagination styles for Laravel paginator */
        nav[role="navigation"] svg { display: inline-block; vertical-align: middle; }
        .pagination-wrap span[aria-current="page"] > span {
            background: #fbbf24 !important;
            color: #1c1917 !important;
            border-color: #fbbf24 !important;
        }
        .pagination-wrap a, .pagination-wrap span {
            border-radius: 0.5rem !important;
        }
    </style>
</head>
<body class="min-h-screen bg-ink-900 text-stone-100 font-sans antialiased">
    @php
        $user = auth()->user();
        $role = optional(optional($user)->role)->role_name ?? 'guest';
        $roleLabel = match($role) {
            'admin' => 'Admin',
            'owner' => 'Owner',
            'tenants', 'tenant' => 'Tenant',
            default => 'Guest',
        };
    @endphp

    @auth
    <header class="border-b border-stone-800/60 bg-ink-800/80 backdrop-blur sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-6">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2" data-testid="brand-link">
                <div class="w-9 h-9 rounded-lg bg-amber-400 text-stone-900 grid place-items-center font-bold">K</div>
                <div class="leading-tight">
                    <div class="font-display text-lg">KostNest</div>
                    <div class="text-[11px] uppercase tracking-[0.18em] text-stone-400">Sistem Rumah Kost</div>
                </div>
            </a>

            <nav class="ml-6 hidden md:flex items-center gap-1">
                @php $current = request()->path(); @endphp
                @if($role === 'owner')
                    @include('partials.nav-link', ['href' => route('kosts.index'), 'label' => 'Kost Saya', 'active' => str_starts_with($current, 'kosts'), 'tid' => 'nav-kosts'])
                    @include('partials.nav-link', ['href' => route('rooms.index'), 'label' => 'Kamar', 'active' => $current === 'rooms', 'tid' => 'nav-rooms'])
                    @include('partials.nav-link', ['href' => route('owner.financials.index'), 'label' => 'Keuangan', 'active' => str_starts_with($current, 'financial'), 'tid' => 'nav-financial'])
                    @include('partials.nav-link', ['href' => route('owner.admins.index'), 'label' => 'Tim Admin', 'active' => str_starts_with($current, 'tim-admin'), 'tid' => 'nav-tim-admin'])
                @elseif($role === 'admin')
                    @include('partials.nav-link', ['href' => route('admin.rooms.index'), 'label' => 'Kelola Kamar', 'active' => str_starts_with($current, 'admin'), 'tid' => 'nav-admin-rooms'])
                @else
                    @include('partials.nav-link', ['href' => route('tenant.rooms.index'), 'label' => 'Cari Kamar', 'active' => str_starts_with($current, 'tenant/rooms'), 'tid' => 'nav-tenant-search'])
                    @include('partials.nav-link', ['href' => route('bookings.index'), 'label' => 'Booking Saya', 'active' => str_starts_with($current, 'bookings'), 'tid' => 'nav-bookings'])
                    @include('partials.nav-link', ['href' => route('payments.index'), 'label' => 'Pembayaran', 'active' => str_starts_with($current, 'payments'), 'tid' => 'nav-payments'])
                @endif
            </nav>

            <div class="ml-auto flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-medium" data-testid="user-name">{{ $user->name }}</div>
                    <div class="text-xs text-amber-400" data-testid="user-role">{{ $roleLabel }}</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-stone-800 grid place-items-center text-sm font-semibold text-amber-300 border border-stone-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" data-testid="logout-button"
                        class="inline-flex items-center gap-1 text-sm text-stone-300 hover:text-stone-100 hover:bg-stone-800 px-3 py-2 rounded-md transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endauth

    <main class="max-w-7xl mx-auto px-6 py-10">
        @include('partials.flash')

        @hasSection('page-header')
            <div class="mb-8">
                @yield('page-header')
            </div>
        @endif

        @yield('content')
    </main>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
      });
    </script>
</body>
</html>
