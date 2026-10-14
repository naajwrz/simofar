
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran QRIS | SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#FFFAFC; color:#493640; font-family:system-ui,sans-serif; }
        .pink { color:#D96C91; }
        .btn-pink { background:#F4A6BB; color:white; border:0; }
        .btn-pink:hover { background:#D96C91; color:white; }
        .panel { background:white; border:1px solid #F5E5EB; border-radius:20px; }
        .qris-box { background:#FFF0F5; border:1px dashed #D96C91; border-radius:16px; }
    </style>
</head>
<body>
<div class="container py-5" style="max-width:650px">
    <a href="{{ $penjualan->id_resep
        ? route('pelanggan.permintaan.index')
        : route('katalog.index') }}" class="text-decoration-none pink">
        <i class="bi bi-arrow-left"></i>
        {{ $penjualan->id_resep ? 'Kembali ke permintaan saya' : 'Kembali ke katalog' }}
    </a>

    <div class="panel p-4 mt-3">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="text-center mb-4">
            <div class="pink display-5"><i class="bi bi-qr-code"></i></div>
            <h1 class="h3 fw-bold mt-2">Pembayaran QRIS</h1>
            <p class="text-secondary mb-0">SIMULASI PEMBAYARAN — bukan QRIS sungguhan</p>
        </div>

        <div class="qris-box p-4 text-center mb-4">
            <div class="text-secondary small">Total yang harus dibayar</div>
            <div class="display-6 fw-bold pink">
                Rp{{ number_format($penjualan->total_tagihan, 0, ',', '.') }}
            </div>
            <div class="mt-3 bg-white rounded p-3">
                <i class="bi bi-qr-code display-1 pink"></i>
                <div class="small text-secondary mt-2">
                    QRIS ilustrasi untuk demo tugas
                </div>
            </div>
            <div class="mt-3">
                <span class="badge text-bg-warning">
                    {{ $penjualan->status_pembayaran === 'lunas'
                        ? 'Pembayaran berhasil (simulasi)'
                        : 'Menunggu konfirmasi' }}
                </span>
            </div>
        </div>

        <h2 class="h6 fw-bold">Detail pesanan</h2>
        <div class="small text-secondary mb-2">
            No. transaksi: {{ $penjualan->no_transaksi }}
        </div>

        @foreach($penjualan->detail as $detail)
            <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                <div>
                    {{ $detail->obat->nama_obat ?? 'Obat' }}
                    <div class="small text-secondary">
                        {{ $detail->jumlah }} ×
                        Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}
                    </div>
                </div>
                <strong>
                    Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                </strong>
            </div>
        @endforeach

        <div class="d-flex justify-content-between mt-3 mb-4">
            <strong>Total</strong>
            <strong class="pink">
                Rp{{ number_format($penjualan->total_tagihan, 0, ',', '.') }}
            </strong>
        </div>

        <div class="small text-secondary mb-3">
            Pelanggan:
            {{ $penjualan->pelanggan->nama_pelanggan ?? '-' }}<br>
            No. HP:
            {{ $penjualan->pelanggan->no_telepon ?? '-' }}
        </div>

        @if($penjualan->status_pembayaran === 'pending')
            <form method="POST" action="{{ $penjualan->id_resep
                ? route('pelanggan.permintaan.pembayaran.confirm', $penjualan->id_resep)
                : route('checkout.confirm') }}">
                @csrf
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox"
                           name="konfirmasi" value="1" id="konfirmasi" required>
                    <label class="form-check-label" for="konfirmasi">
                        Saya mengonfirmasi pembayaran berhasil (simulasi).
                    </label>
                </div>
                <button type="submit" class="btn btn-pink w-100 py-2">
                    <i class="bi bi-check-circle me-1"></i>
                    Konfirmasi Pembayaran
                </button>
            </form>
        @else
            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle-fill me-1"></i>
                Pembayaran simulasi telah dikonfirmasi.
                Status pesanan: {{ str_replace('_', ' ', $penjualan->status_pesanan) }}.
            </div>
        @endif
    </div>
</div>
@if($penjualan->status_pembayaran === 'lunas')
<script>
    // Pembayaran berhasil: kosongkan keranjang di browser.
    localStorage.removeItem('simofar_cart_v1');
</script>
@endif
</body>
</html>
