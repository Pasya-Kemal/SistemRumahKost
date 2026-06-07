@if(session('success'))
    <div data-testid="flash-success" class="mb-6 flex items-start gap-3 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
        <i data-lucide="check-circle-2" class="w-5 h-5 mt-0.5 shrink-0"></i>
        <div class="text-sm">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div data-testid="flash-error" class="mb-6 flex items-start gap-3 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300">
        <i data-lucide="alert-triangle" class="w-5 h-5 mt-0.5 shrink-0"></i>
        <div class="text-sm">{{ session('error') }}</div>
    </div>
@endif

@if($errors->any())
    <div data-testid="flash-errors" class="mb-6 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300">
        <div class="flex items-center gap-2 text-sm font-semibold mb-1">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i> Ada {{ $errors->count() }} kesalahan input
        </div>
        <ul class="list-disc pl-6 text-sm space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
