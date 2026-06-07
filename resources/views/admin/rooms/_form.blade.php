@props(['room' => null, 'kosts' => collect(), 'action', 'method' => 'POST'])
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div>
        <label class="text-sm text-stone-300">Pilih Kost</label>
        <select name="kost_id" data-testid="admin-room-kost-select" required
                class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">
            <option value="">— Pilih Kost —</option>
            @foreach($kosts as $k)
                <option value="{{ $k->id }}" @selected(old('kost_id', optional($room)->kost_id) == $k->id)>
                    {{ $k->kost_name }} ({{ $k->kost_gender }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-sm text-stone-300">Nama Kamar</label>
        <input name="room_name" data-testid="admin-room-name-input" required
               value="{{ old('room_name', optional($room)->room_name) }}"
               class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400" />
    </div>

    <div>
        <label class="text-sm text-stone-300">Harga / Bulan (Rp)</label>
        <input type="number" min="0" name="price" data-testid="admin-room-price-input" required
               value="{{ old('price', optional($room)->price) }}"
               class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400" />
    </div>

    <div>
        <label class="text-sm text-stone-300">Deskripsi</label>
        <textarea name="description" data-testid="admin-room-desc-input" rows="3" required
                  class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 focus:outline-none focus:border-amber-400">{{ old('description', optional($room)->description) }}</textarea>
    </div>

    <label class="flex items-center justify-between rounded-md border border-stone-800 px-3 py-2 cursor-pointer">
        <span class="text-sm text-stone-300">Tersedia untuk Booking</span>
        <input type="checkbox" name="is_available" value="1" data-testid="admin-room-available-checkbox"
               @checked(old('is_available', optional($room)->is_available ?? true))
               class="w-4 h-4 accent-amber-400">
    </label>

    @if($method === 'POST')
    <div>
        <label class="text-sm text-stone-300">Foto Kamar (opsional, bisa pilih beberapa)</label>
        <input type="file" name="photos[]" accept="image/*" multiple data-testid="admin-room-photos-input"
               class="mt-1 w-full bg-stone-900 border border-stone-800 rounded-md px-3 py-2 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-amber-400 file:text-stone-900 file:font-semibold">
        <p class="text-xs text-stone-500 mt-1">JPG/PNG · max 2MB · foto pertama jadi foto utama</p>
    </div>
    @endif

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" data-testid="admin-room-submit"
            class="bg-amber-400 hover:bg-amber-500 text-stone-900 font-semibold px-5 py-2.5 rounded-md transition">
            Simpan
        </button>
        <a href="{{ route('admin.rooms.index') }}" class="text-stone-400 hover:text-stone-200 text-sm">Batal</a>
    </div>
</form>
