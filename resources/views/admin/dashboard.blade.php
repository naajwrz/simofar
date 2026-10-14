@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
@php
    $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
    $kartu = [
        ['Omzet Hari Ini', $rp($omzetHariIni), 'bi-cash-coin', 'success'],
        ['Omzet Bulan Ini', $rp($omzetBulanIni), 'bi-graph-up-arrow', 'primary'],
        ['Estimasi Laba Bulan Ini', $rp($labaBulanIni), 'bi-piggy-bank', 'success'],
        ['Modal Pengadaan Bulan Ini', $rp($modalPengadaan), 'bi-truck', 'warning'],
        ['Stok Menipis', $stokMenipis . ' obat', 'bi-exclamation-triangle', 'danger'],
        ['Hampir Kedaluwarsa (30 hari)', $hampirED . ' obat', 'bi-hourglass-split', 'warning'],
        ['Resep Perlu Diproses', $resepMenunggu . ' resep', 'bi-file-earmark-medical', 'primary'],
        ['Nilai Persediaan', $rp($nilaiPersediaan), 'bi-box-seam', 'secondary'],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Admin</h3>
        <p class="text-muted small mb-0">
            {{ $trxBulanIni }} transaksi lunas bulan ini ·
            {{ $jumlahApoteker }} apoteker aktif · {{ $jumlahPelanggan }} pelanggan
        </p>
    </div>
    <a href="{{ route('admin.diagram') }}" class="btn btn-outline-pink rounded-pill px-3">
        <i class="bi bi-diagram-3 me-1"></i> Diagram Sistem Informasi
    </a>
</div>

<div class="row g-3 mb-4">
    @foreach($kartu as [$judul, $nilai, $ikon, $warna])
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-{{ $warna }}-subtle text-{{ $warna }}">
                        <i class="bi {{ $ikon }}"></i>
                    </div>
                    <div>
                        <small class="text-muted">{{ $judul }}</small>
                        <div class="fw-bold">{{ $nilai }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold">Omzet 14 Hari Terakhir</h6>
                <canvas id="chartOmzet" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold">5 Obat Terlaris</h6>
                <canvas id="chartTerlaris" height="200"></canvas>
                @if($terlaris->isNotEmpty())
                    <div class="mt-3">
                        @foreach($terlaris as $obatPopuler)
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong class="small">{{ $obatPopuler->nama_obat }}</strong>
                                    <span class="small text-muted text-nowrap">
                                        {{ number_format($obatPopuler->terjual) }} terjual ·
                                        {{ number_format($obatPopuler->jumlah_pelanggan) }} pelanggan
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    Indikasi tercatat: {{ $obatPopuler->indikasi ?: 'Belum dicatat.' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="small text-muted mt-3 mb-0">
                        Indikasi adalah informasi penggunaan obat, bukan diagnosis atau data penyakit pelanggan.
                    </p>
                @else
                    <p class="text-muted small mb-0">Belum ada penjualan lunas.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold">Pengadaan per Supplier (30 hari)</h6>
                @if($labelSupplier->isEmpty())
                    <p class="text-muted small mb-0">Belum ada pengadaan.</p>
                @else
                    <canvas id="chartSupplier" height="220"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Pengadaan Terbaru</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>No. Faktur</th><th>Tanggal</th><th>Supplier</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            @forelse($pengadaanTerbaru as $b)
                                <tr>
                                    <td class="fw-semibold">{{ $b->no_faktur }}</td>
                                    <td>{{ \Carbon\Carbon::parse($b->tanggal_pengadaan)->format('d/m/Y') }}</td>
                                    <td>{{ $b->supplier->nama_supplier ?? '-' }}</td>
                                    <td class="text-end">{{ $rp($b->total_modal) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pengadaan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Transaksi Terbaru</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No. Transaksi</th><th>Tanggal</th><th>Pelanggan</th><th>Obat dalam transaksi</th><th>Total</th><th>Metode</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td class="fw-semibold">{{ $t->no_transaksi }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                            <td>
                                @forelse($t->detail as $detail)
                                    <div>
                                        {{ $detail->obat->nama_obat ?? 'Obat tidak tersedia' }}
                                        <span class="text-muted">× {{ $detail->jumlah }}</span>
                                    </div>
                                @empty
                                    <span class="text-muted">Tidak ada rincian obat.</span>
                                @endforelse
                            </td>
                            <td>{{ $rp($t->total_tagihan) }}</td>
                            <td class="text-uppercase small">{{ $t->metode_bayar }}</td>
                            <td>
                                <span class="badge text-bg-{{ $t->status_pembayaran === 'lunas' ? 'success' : 'warning' }}">
                                    {{ $t->status_pembayaran ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.js"></script>
<script>
    new Chart(document.getElementById('chartOmzet'), {
        type: 'line',
        data: {
            labels: @json($labelHarian),
            datasets: [{
                label: 'Omzet (Rp)',
                data: @json($dataHarian),
                borderColor: '#ff85a1',
                backgroundColor: 'rgba(255,133,161,.15)',
                fill: true,
                tension: .3
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('chartTerlaris'), {
        type: 'bar',
        data: {
            labels: @json($labelTerlaris),
            datasets: [{ label: 'Terjual', data: @json($dataTerlaris), backgroundColor: '#ff85a1' }]
        },
        options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });
    @if($labelSupplier->isNotEmpty())
new Chart(document.getElementById('chartSupplier'), {
    type: 'doughnut',
    data: {
        labels: @json($labelSupplier),
        datasets: [{
            data: @json($dataSupplier),
            backgroundColor: ['#ff85a1', '#ffb3c6', '#f76c8c', '#ffcad4', '#d63384', '#fde2e4']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
@endif
</script>
@endpush