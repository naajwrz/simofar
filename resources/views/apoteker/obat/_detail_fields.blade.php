@php
    $o = $o ?? null;
    $suppliers = \App\Models\Supplier::orderBy('nama_supplier')->get();
    $infoFields = [
        'deskripsi' => 'Deskripsi obat',
        'indikasi' => 'Indikasi / kegunaan',
        'aturan_pakai' => 'Aturan pakai',
        'efek_samping' => 'Efek samping',
        'kontraindikasi' => 'Kontraindikasi / perhatian',
        'catatan_apoteker' => 'Catatan apoteker',
    ];
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Supplier</label>
        <select name="id_supplier" class="form-select" required>
            <option value="">-- Pilih Supplier --</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id_supplier }}" {{ $o?->id_supplier == $s->id_supplier ? 'selected' : '' }}>
                    {{ $s->nama_supplier }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Golongan</label>
        @php $gol = $o?->golongan ?? 'bebas'; @endphp
        <select name="golongan" class="form-select" required>
            <option value="bebas" {{ $gol === 'bebas' ? 'selected' : '' }}>Obat bebas</option>
            <option value="bebas_terbatas" {{ $gol === 'bebas_terbatas' ? 'selected' : '' }}>Bebas terbatas</option>
            <option value="keras" {{ $gol === 'keras' ? 'selected' : '' }}>Obat keras (perlu resep)</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Harga Beli</label>
        <input type="number" name="harga_beli" class="form-control" min="0" step="0.01"
               value="{{ $o?->harga_beli ?? 0 }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Harga Jual</label>
        <input type="number" name="harga_jual" class="form-control" min="0" step="0.01"
               value="{{ $o?->harga_jual ?? 0 }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Stok Minimum</label>
        <input type="number" name="stok_minimum" class="form-control" min="0"
               value="{{ $o?->stok_minimum ?? 10 }}" required>
    </div>
</div>


<hr>
<p class="small text-muted">Informasi ini tampil ke pelanggan di katalog (Detail Obat).</p>

@foreach($infoFields as $field => $label)
    <div class="mb-3">
        <label class="form-label fw-semibold">{{ $label }}</label>
        <textarea name="{{ $field }}" class="form-control" rows="2">{{ $o?->$field }}</textarea>
    </div>
      @include('apoteker.obat._detail_fields', ['o' => $o])
@endforeach