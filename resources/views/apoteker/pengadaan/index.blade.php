<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengadaan & Pembelian Obat - SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .bg-pink { background-color: #ff85a1; }
        .text-pink { color: #ff85a1; }
        .btn-pink { background-color: #ff85a1; color: white; }
        .btn-pink:hover { background-color: #f76c8c; color: white; }
        .btn-outline-pink { border-color: #ff85a1; color: #ff85a1; }
        .btn-outline-pink:hover { background-color: #ff85a1; color: white; }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-pink mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-capsule-pill me-2"></i>SIMOFAR - Pembelian
            </a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small"><i class="bi bi-person-circle me-1"></i> {{ auth()->user()->nama_staf ?? 'Petugas' }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                </form>
            </div>
        </div>
    </nav>

   
<div class="container mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Pengadaan Obat</h3>
            <p class="text-muted small mb-0">
                Riwayat transaksi stok obat dari supplier
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="button"
                    class="btn btn-outline-pink rounded-pill px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalSupplier">
                <i class="bi bi-truck me-1"></i> Tambah Supplier
            </button>

            <button type="button"
                    class="btn btn-pink rounded-pill px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalPengadaan">
                <i class="bi bi-plus-circle me-1"></i> Pengadaan Obat
            </button>

            <a href="{{ route('apoteker.dashboard') }}"
               class="btn btn-outline-pink rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Pengadaan gagal disimpan:</strong>
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Data belum berhasil disimpan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

  


        <!-- Card Tabel Pembelian -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-pink bg-opacity-10 text-pink">
                            <tr>
                                <th class="ps-3 py-3">No. Faktur</th>
                                <th class="py-3">Supplier</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Detail Obat</th>
                                <th class="py-3">Total Biaya</th>
                                <th class="py-3">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembelianList as $pembelian)
                                <tr>
                                    <td class="ps-3">{{ $pembelian->no_faktur }}</td>
                                    <td>{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pengadaan)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @forelse ($pembelian->details as $d)
                                                <li>
                                                    <strong>{{ $d->obat->nama_obat ?? 'Obat dihapus' }}</strong>
                                                    × {{ $d->jumlah }}
                                                    @ Rp{{ number_format($d->harga_beli, 0, ',', '.') }}
                                                </li>
                                            @empty
                                                <li>Tidak ada detail pembelian.</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>Rp{{ number_format($pembelian->total_modal ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $pembelian->user->nama_staf ?? 'Petugas' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengadaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="modalSupplier" tabindex="-1"
     aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header text-white"
                 style="background:#ff85a1">
                <h5 class="modal-title fw-bold" id="modalSupplierLabel">
                    <i class="bi bi-truck me-2"></i>Tambah Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('apoteker.supplier.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Supplier</label>
                        <input type="text" name="kode_supplier"
                               class="form-control" maxlength="10"
                              value="{{ old('kode_supplier', $kodeSupplierBaru) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="nama_supplier"
                               class="form-control" maxlength="100"
                               placeholder="Nama perusahaan supplier" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="no_telepon"
                               class="form-control" maxlength="15"
                               placeholder="Nomor telepon supplier">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control"
                                  rows="2" placeholder="Alamat supplier"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Kerja Sama</label>
                        <select name="status_kerjasama" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-pink rounded-pill">
                        Simpan Supplier
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- Modal Pengadaan Obat -->
<div class="modal fade" id="modalPengadaan" tabindex="-1"
     aria-labelledby="modalPengadaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header text-white"
                 style="background:#ff85a1">
                <h5 class="modal-title fw-bold" id="modalPengadaanLabel">
                    <i class="bi bi-box-seam me-2"></i>Pengadaan Obat
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('apoteker.pengadaan.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    @if(session('error') || $errors->any())
                        <div class="alert alert-danger">
                            <strong>Pengadaan belum berhasil disimpan:</strong>
                            @if(session('error'))
                                {{ session('error') }}
                            @endif
                            @if($errors->any())
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor Faktur</label>
                            <input type="text" name="no_faktur"
                                   class="form-control"
                                   value="{{ old('no_faktur', $noFakturBaru) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pengadaan</label>
                            <input type="date" name="tanggal_pengadaan"
                                   class="form-control"
                                   value="{{ old('tanggal_pengadaan', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Supplier</label>
                            <select name="id_supplier" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}"
                                        @selected(old('id_supplier') == $supplier->id_supplier)>
                                        {{ $supplier->kode_supplier }} -
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3">Detail Obat</h6>
                    <div id="itemPengadaan">
                        @php($oldItems = old('items', [[]]))
                        @foreach($oldItems as $index => $oldItem)
                            <div class="row g-2 item-row mb-2">
                                <div class="col-md-6">
                                    <select name="items[{{ $index }}][id_obat]" class="form-select" required>
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach($obats as $obat)
                                            <option value="{{ $obat->id_obat }}"
                                                @selected(($oldItem['id_obat'] ?? '') == $obat->id_obat)>
                                                {{ $obat->nama_obat }} (Stok: {{ $obat->stok }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[{{ $index }}][jumlah]"
                                           class="form-control" min="1" placeholder="Jumlah"
                                           value="{{ $oldItem['jumlah'] ?? '' }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[{{ $index }}][harga_beli]"
                                           class="form-control" min="0" step="0.01"
                                           placeholder="Harga beli"
                                           value="{{ $oldItem['harga_beli'] ?? '' }}" required>
                                </div>
                                <div class="col-md-1">
                                    @if($index > 0)
                                        <button type="button" class="btn btn-outline-danger btn-hapus-item"
                                                aria-label="Hapus obat">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <template id="templateItemPengadaan">
                        <div class="row g-2 item-row mb-2">
                            <div class="col-md-6">
                                <select class="form-select" data-field="id_obat" required>
                                    <option value="">-- Pilih Obat --</option>
                                    @foreach($obats as $obat)
                                        <option value="{{ $obat->id_obat }}">
                                            {{ $obat->nama_obat }} (Stok: {{ $obat->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" data-field="jumlah"
                                       min="1" placeholder="Jumlah" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" data-field="harga_beli"
                                       min="0" step="0.01" placeholder="Harga beli" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-hapus-item"
                                        aria-label="Hapus obat">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                        <button type="button" id="tambahItem"
                                class="btn btn-sm btn-outline-pink rounded-pill">
                            <i class="bi bi-plus-lg"></i> Tambah Obat
                        </button>
                        <span class="small text-muted">
                            <i class="bi bi-info-circle me-1 text-pink"></i>
                            Stok obat bertambah setelah transaksi berhasil disimpan.
                        </span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-pink rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>
                        Simpan Pengadaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

    <!-- Footer Section -->
    <footer class="bg-white border-top mt-auto py-4">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-5">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-capsule-pill fs-3 text-pink me-2"></i>
                        <h5 class="fw-bold mb-0 text-dark">SIMOFAR</h5>
                    </div>
                    <p class="text-muted small mb-2">
                        Sistem Informasi Manajemen Obat & Farmasi Terpadu untuk mengoptimalkan pengelolaan stok, resep, dan pengadaan obat secara akurat.
                    </p>
                    <small class="text-muted d-block">&copy; 2026 SIMOFAR. All Rights Reserved.</small>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-headset text-pink me-2"></i>Kontak Support & Admin</h6>
                    <ul class="list-unstyled text-muted small lh-lg mb-0">
                        <li><i class="bi bi-envelope me-2 text-pink"></i> admin.simofar@uin-malang.ac.id</li>
                        <li><i class="bi bi-telephone me-2 text-pink"></i> +62 812-3456-7890 (Helpdesk)</li>
                        <li><i class="bi bi-geo-alt me-2 text-pink"></i> Gedung Lab. Komputer, UIN Maulana Malik Ibrahim Malang</li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-code-slash text-pink me-2"></i>Tim Pengembang</h6>
                    <ul class="list-unstyled text-muted small lh-lg mb-0">
                        <li><i class="bi bi-person-circle me-2 text-pink"></i> Najwarizqa Aryandri</li>
                        <li><i class="bi bi-person-circle me-2 text-pink"></i> Aldza Salwatul</li>
                        <li class="mt-1 text-pink fw-semibold"><i class="bi bi-building me-2"></i> UIN Maulana Malik Ibrahim Malang</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script>
    const itemContainer = document.getElementById('itemPengadaan');
    const itemTemplate = document.getElementById('templateItemPengadaan');
    const addItemButton = document.getElementById('tambahItem');
    let idxItem = itemContainer.querySelectorAll('.item-row').length;

    addItemButton.addEventListener('click', () => {
        const row = itemTemplate.content.firstElementChild.cloneNode(true);
        row.querySelector('[data-field="id_obat"]').name = `items[${idxItem}][id_obat]`;
        row.querySelector('[data-field="jumlah"]').name = `items[${idxItem}][jumlah]`;
        row.querySelector('[data-field="harga_beli"]').name = `items[${idxItem}][harga_beli]`;
        itemContainer.appendChild(row);
        idxItem++;
    });

    itemContainer.addEventListener('click', event => {
        if (!(event.target instanceof Element)) return;
        const removeButton = event.target.closest('.btn-hapus-item');
        if (removeButton) removeButton.closest('.item-row').remove();
    });

    @if($errors->any() || session('error'))
        window.addEventListener('load', () => new bootstrap.Modal(document.getElementById('modalPengadaan')).show());
    @endif
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>