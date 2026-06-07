@extends('layouts.app')
@section('title', 'Kost Saya')

@section('page-header')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl md:text-4xl">Kost Saya</h1>
            <p class="text-stone-400 mt-2">Kelola seluruh properti kost yang Anda miliki.</p>
        </div>
        <a href="{{ route('kosts.create') }}" data-testid="add-kost-button"
           class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-4 py-2.5 rounded-md transition">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kost
        </a>
    </div>
@endsection

@section('content')
@if($kosts->isEmpty())
    <div class="p-12 text-center rounded-xl border border-dashed border-stone-800 text-stone-500">
        Belum ada kost. Klik <span class="text-amber-300">Tambah Kost</span> untuk membuat properti pertama Anda.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($kosts as $kost)
            <div data-testid="kost-card-{{ $kost->id }}"
                 class="rounded-xl border border-stone-800 bg-ink-800 p-5 hover:border-amber-400/40 transition flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-semibold text-lg">{{ $kost->kost_name }}</h3>
                        <span class="inline-flex mt-1 px-2 py-0.5 text-[11px] font-medium rounded border border-amber-400/30 bg-amber-400/10 text-amber-300">
                            {{ $kost->kost_gender }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('kosts.edit', $kost) }}" data-testid="edit-kost-{{ $kost->id }}"
                           class="p-2 rounded-md text-stone-300 hover:bg-stone-800">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('kosts.destroy', $kost) }}"
                              onsubmit="return confirm('Hapus kost {{ $kost->kost_name }} beserta kamar-kamarnya?')">
                            @csrf @method('DELETE')
                            <button type="submit" data-testid="delete-kost-{{ $kost->id }}"
                                class="p-2 rounded-md text-red-400 hover:bg-red-500/10">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-sm text-stone-400 mt-3 line-clamp-2">{{ $kost->address }}</p>
                <div class="mt-auto pt-4 flex items-center justify-between text-xs">
                    <span class="text-stone-500 inline-flex items-center gap-1">
                        <i data-lucide="door-open" class="w-3 h-3"></i> {{ $kost->rooms()->count() }} kamar
                    </span>
                    <a href="{{ route('kosts.show', $kost) }}" class="text-amber-300 hover:underline">Lihat detail →</a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
