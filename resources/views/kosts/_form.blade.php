@props(['kost' => null, 'action', 'method' => 'POST'])
<form method="POST" action="{{ $action }}" class="space-y-5 max-w-2xl">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div>
        <label class="text-sm text-stone-300">Nama Kost</label>
        <input name="kost_name" data-testid="kost-name-input" required
               value="{{ old('kost_name', optional($kost)->kost_name) }}"
               class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400" />
    </div>

    <div>
        <label class="text-sm text-stone-300">Jenis Kost</label>
        <select name="kost_gender" data-testid="kost-gender-select"
                class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
            @php $g = old('kost_gender', optional($kost)->kost_gender ?? 'Campuran'); @endphp
            <option value="Pria" @selected($g === 'Pria')>Pria</option>
            <option value="Wanita" @selected($g === 'Wanita')>Wanita</option>
            <option value="Campuran" @selected($g === 'Campuran')>Campuran</option>
        </select>
    </div>

    <div>
        <label class="text-sm text-stone-300">Alamat Lengkap</label>
        <textarea name="address" data-testid="kost-address-input" rows="3" required
                  class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">{{ old('address', optional($kost)->address) }}</textarea>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" data-testid="kost-submit-button"
            class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-5 py-2.5 rounded-md transition">
            Simpan
        </button>
        <a href="{{ route('kosts.index') }}" class="text-stone-400 hover:text-stone-200 text-sm">Batal</a>
    </div>
</form>
