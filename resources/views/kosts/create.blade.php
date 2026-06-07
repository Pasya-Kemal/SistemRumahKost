@extends('layouts.app')
@section('title', 'Tambah Kost')

@section('page-header')
    <a href="{{ route('kosts.index') }}" class="text-sm text-stone-400 hover:text-stone-200 inline-flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
    </a>
    <h1 class="font-display text-3xl md:text-4xl">Tambah Kost Baru</h1>
    <p class="text-stone-400 mt-2">Isi detail properti kost yang akan Anda kelola.</p>
@endsection

@section('content')
<div class="rounded-xl border border-stone-800 bg-ink-800 p-6 md:p-8">
    @include('kosts._form', ['action' => route('kosts.store'), 'method' => 'POST'])
</div>
@endsection
