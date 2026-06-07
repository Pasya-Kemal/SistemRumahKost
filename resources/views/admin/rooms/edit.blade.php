@extends('layouts.app')
@section('title', 'Edit Kamar')

@section('page-header')
    <a href="{{ route('admin.rooms.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Edit Kamar</h1>
    <p class="text-stone-400 mt-2">Perbarui informasi kamar <span class="text-amber-300">{{ $room->room_name }}</span>.</p>
@endsection

@section('content')
<div class="rounded-xl border border-stone-800 bg-ink-800 p-6 md:p-8">
    @include('admin.rooms._form', ['room' => $room, 'kosts' => $kosts, 'action' => route('admin.rooms.update', $room), 'method' => 'PUT'])
</div>
@endsection
