<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Obat Keras | SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#FFFAFC; color:#493640; font-family:system-ui,sans-serif; }
        .pink { color:#D96C91; }
        .panel { background:white; border:1px solid #F5E5EB; border-radius:18px; }
    </style>
</head>
<body>
@php
    $label = [
        'diajukan' => ['Menunggu persetujuan apoteker', 'warning'],
        'menunggu' => ['Sedang diverifikasi apoteker', 'info'],
        'valid' => ['Resep valid', 'success'],
        'tidak_valid' => ['Ditolak / tidak valid', 'danger'],
        'terpakai' => ['Sudah dipakai', 'secondary'],
    ];
@endphp
<div class="container py-5" style="max-width:760px">
    <a href="{{ route('katalog.index') }}" class="text-decoration-none pink">
        <i class="bi bi-arrow-left"></i> Kembali ke katalog
    </a>
    <h1 class="h3 fw-bold mt-3 mb-3">Permintaan Obat Keras</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($permintaan as $r)
        @php [$teks, $warna] = $label[$r->status_validasi] ?? [$r->status_validasi, 'secondary']; @endphp
        <div class="panel p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <strong>{{ $r->no_resep }}</strong>
                <span class="badge text-bg-{{ $warna }}">{{ $teks }}</span>
            </div>
            <div class="small text-secondary mb-2">
                Dokter: {{ $r->nama_dokter }} · Tanggal resep:
                {{ \Carbon\Carbon::parse($r->tanggal_terbit)->format('d/m/Y') }}
            </div>
            @foreach($r->detailResep as $d)
                <div>
                    <i class="bi bi-capsule pink me-1"></i>
                    <strong>{{ $d->obat->nama_obat ?? 'Obat' }}</strong> × {{ $d->jumlah }}
                    <span class="small text-secondary">{{ $d->aturan_pakai }}</span>
                </div>
            @endforeach
        </div>
    @empty
        <div class="panel p-5 text-center text-secondary">
            Belum ada permintaan. Pilih obat keras di katalog lalu klik "Perlu Resep".
        </div>
    @endforelse
</div>
</body>
</html>