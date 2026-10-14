@extends('admin.layout')
@section('title', 'Keuangan')

@section('content')
@php
    $rp = fn ($nilai) => 'Rp' . number_format((float) $nilai, 0, ',', '.');
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Keuangan & Omzet</h3>
        <p class="text-muted small mb-0">Omzet dihitung dari transaksi yang sudah lunas.</p>
    </div>
    <form method="GET" action="{{ route('admin.keuangan') }}" class="d-flex gap-2 align-items-center">
        <label for="tahunKeuangan" class="small text-muted">Tahun</label>
        <select id="tahunKeuangan" name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($pilihanTahun = now()->year; $pilihanTahun >= 2000; $pilihanTahun--)
                <option value="{{ $pilihanTahun }}" @selected($tahun === $pilihanTahun)>{{ $pilihanTahun }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="alert alert-info border-0 rounded-4 small">
    <i class="bi bi-info-circle me-1"></i>
    Saldo berjalan dihitung dari saldo awal + seluruh omzet penjualan lunas − gaji − pajak − operasional.
    Pajak dihitung dari omzet lunas bulanan. Gaji dihitung dari gaji per apoteker × jumlah apoteker.
    Bulan berstatus “Belum diatur” belum memasukkan gaji, pajak, dan operasional ke saldo bersih.
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Omzet Lunas ' . $tahun, $omzetTahunan, 'bi-cash-stack', 'primary'],
        ['Penggajian Apoteker', $gajiTahunan, 'bi-people', 'warning'],
        ['Pajak', $pajakTahunan, 'bi-receipt', 'danger'],
        ['Operasional', $operasionalTahunan, 'bi-building-gear', 'secondary'],
        ['Saldo Bersih Tercatat', $saldoTahunan, 'bi-wallet2', 'success'],
    ] as [$judul, $nilai, $ikon, $warna])
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="text-{{ $warna }} fs-4 mb-2"><i class="bi {{ $ikon }}"></i></div>
                    <small class="text-muted">{{ $judul }}</small>
                    <div class="fw-bold">{{ $rp($nilai) }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">Atur Keuangan Bulanan</h5>
                <p class="small text-muted mb-0">Nilai disimpan sebagai riwayat tetap untuk bulan yang dipilih.</p>
            </div>
            <span class="badge text-bg-light">Apoteker aktif saat ini: {{ $jumlahApotekerAktif }}</span>
        </div>

        <form method="POST" action="{{ route('admin.keuangan.save') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="bulan" class="form-label">Bulan</label>
                    <input type="month" class="form-control" id="bulan" name="bulan"
                           value="{{ old('bulan', $formKeuangan['bulan']) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="gajiPerApoteker" class="form-label">Gaji per apoteker / bulan</label>
                    <input type="number" class="form-control" id="gajiPerApoteker"
                           name="gaji_per_apoteker" min="0" step="1000"
                           value="{{ old('gaji_per_apoteker', $formKeuangan['gaji_per_apoteker']) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="jumlahApoteker" class="form-label">Jumlah apoteker yang digaji</label>
                    <input type="number" class="form-control" id="jumlahApoteker"
                           name="jumlah_apoteker" min="0" max="1000"
                           value="{{ old('jumlah_apoteker', $formKeuangan['jumlah_apoteker']) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="pajakPersen" class="form-label">Pajak dari omzet lunas (%)</label>
                    <input type="number" class="form-control" id="pajakPersen"
                           name="pajak_persen" min="0" max="100" step="0.01"
                           value="{{ old('pajak_persen', $formKeuangan['pajak_persen']) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="biayaOperasional" class="form-label">Biaya operasional bulan terpilih</label>
                    <input type="number" class="form-control" id="biayaOperasional"
                           name="biaya_operasional" min="0" step="1000"
                           value="{{ old('biaya_operasional', $formKeuangan['biaya_operasional']) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="saldoAwal" class="form-label">Saldo awal</label>
                    <input type="number" class="form-control" id="saldoAwal"
                           name="saldo_awal" min="0" step="1000"
                           value="{{ old('saldo_awal', $saldoAwal) }}" required>
                    <div class="form-text">Nilai awal saat memulai pencatatan keuangan. Default Rp2.000.000.</div>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <small class="text-muted">
                    Potongan bulan terpilih akan diperbarui mengikuti nilai yang disimpan.
                </small>
                <button type="submit" class="btn btn-pink rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Bulan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Saldo Berjalan Sepanjang Waktu</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted">Saldo awal</small>
                    <div class="h5 fw-bold mb-0">{{ $rp($saldoAwal) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted">Total penjualan lunas</small>
                    <div class="h5 fw-bold mb-0">{{ $rp($omzetSepanjangWaktu) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-success-subtle rounded-3">
                    <small class="text-muted">Saldo berjalan setelah beban tercatat</small>
                    <div class="h5 fw-bold mb-0 text-success">{{ $rp($saldoSepanjangWaktu) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Riwayat Keuangan {{ $tahun }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bulan</th>
                        <th class="text-end">Omzet Lunas</th>
                        <th class="text-end">Gaji Apoteker</th>
                        <th class="text-end">Pajak</th>
                        <th class="text-end">Operasional</th>
                        <th class="text-end">Saldo Bersih Tercatat</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatBulanan as $baris)
                        <tr>
                            <td class="fw-semibold">{{ $baris['periode'] }}</td>
                            <td class="text-end">{{ $rp($baris['omzet']) }}</td>
                            <td class="text-end">{{ $rp($baris['gaji']) }}</td>
                            <td class="text-end">{{ $rp($baris['pajak']) }}</td>
                            <td class="text-end">{{ $rp($baris['operasional']) }}</td>
                            <td class="text-end fw-semibold">
                                {{ $baris['tercatat'] ? $rp($baris['saldo']) : 'Belum dihitung' }}
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $baris['tercatat'] ? 'success' : 'secondary' }}">
                                    {{ $baris['tercatat'] ? 'Tercatat' : 'Belum diatur' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.keuangan', ['tahun' => $tahun, 'bulan' => sprintf('%04d-%02d', $tahun, $baris['bulan'])]) }}"
                                   class="btn btn-sm btn-outline-pink rounded-pill">Atur</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Total {{ $tahun }}</td>
                        <td class="text-end">{{ $rp($omzetTahunan) }}</td>
                        <td class="text-end">{{ $rp($gajiTahunan) }}</td>
                        <td class="text-end">{{ $rp($pajakTahunan) }}</td>
                        <td class="text-end">{{ $rp($operasionalTahunan) }}</td>
                        <td class="text-end">{{ $rp($saldoTahunan) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
