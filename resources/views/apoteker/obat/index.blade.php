<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok & Expired - SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .bg-pink { background-color: #ff85a1; }
        .btn-pink { background-color: #ff85a1; color: white; }
        .btn-pink:hover { background-color: #f76c8c; color: white; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-pink shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('apoteker.dashboard') }}"><i class="bi bi-capsule-pill me-2"></i>SIMOFAR - Apoteker</a>
            <a href="{{ route('apoteker.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Header & Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Kelola Obat & Kategori</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    <i class="bi bi-tag-fill me-1"></i> + Kategori Baru
                </button>
                <button class="btn btn-pink rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahObat">
                    <i class="bi bi-plus-lg me-1"></i> + Tambah Obat Baru
                </button>
            </div>
        </div>

      @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($obatMendekatiExpired) && $obatMendekatiExpired->count() > 0)
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-2 me-3 text-warning"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Peringatan Kadaluarsa!</h6>
                        <p class="mb-0 small">Ada <strong>{{ $obatMendekatiExpired->count() }} obat</strong> yang mendekati atau sudah kadaluarsa dalam 30 hari ke depan.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabel Data Obat -->
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Stok Saat Ini</th>
                            <th>Tanggal Expired</th>
                            <th>Status Expired</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($obats as $o)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $o->kode_obat }}</span></td>
                            <td class="fw-bold">{{ $o->nama_obat }}</td>
                            <td>
                                <span class="badge bg-light text-dark border fw-semibold">
                                    {{ $o->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $o->stok }} {{ $o->satuan }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($o->tanggal_expired)->format('d M Y') }}</td>
                            <td>
                                @php
                                    $exp = \Carbon\Carbon::parse($o->tanggal_expired);
                                    $sisaHari = (int) \Carbon\Carbon::now()->diffInDays($exp, false);
                                @endphp

                                @if($sisaHari < 0)
                                    <span class="badge bg-danger">Sudah ED</span>
                                @elseif($sisaHari <= 30)
                                    <span class="badge bg-warning text-dark">ED < {{ $sisaHari }} Hari</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-pink rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $o->id_obat }}">
                                        <i class="bi bi-pencil-square me-1"></i> Edit Stok/ED
                                    </button>
                                    <form action="{{ route('apoteker.obat.destroy', $o->id_obat) }}" method="POST"
                                          onsubmit="return confirm('Hapus obat ini dari daftar? Riwayat transaksi tetap disimpan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Stok & ED -->
                        <div class="modal fade" id="modalEdit{{ $o->id_obat }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-pink text-white">
                                        <h5 class="modal-title fw-bold">Update Obat: {{ $o->nama_obat }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('apoteker.obat.update', $o->id_obat) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jumlah Stok Baru</label>
                                                <input type="number" name="stok" class="form-control" value="{{ $o->stok }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tanggal Kadaluarsa (ED)</label>
                                                <input type="date" name="tanggal_expired" class="form-control" value="{{ $o->tanggal_expired }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-pink rounded-3">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    

    <!-- Modal Tambah Obat Baru -->
    <div class="modal fade" id="modalTambahObat" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-pink text-white">
                    <h5 class="modal-title fw-bold">Tambah Obat Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('apoteker.obat.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Obat</label>
                            <input type="text" name="kode_obat" class="form-control" placeholder="Contoh: OBT-004" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Obat</label>
                            <input type="text" name="nama_obat" class="form-control" placeholder="Contoh: Ibuprofen 400mg" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori Obat</label>
                           <select name="id_kategori" class="form-select" required>
    <option value="">-- Pilih Kategori --</option>
    @foreach(\App\Models\KategoriObat::all() as $kat)
        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
    @endforeach
</select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Satuan</label>
                                <input type="text" name="satuan" class="form-control" placeholder="strip / botol / box" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_expired" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-pink rounded-3">Simpan Obat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kategori Baru -->
    <div class="modal fade" id="modalTambahKategori" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('apoteker.kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Kategori</label>
                            <input type="text" name="kode_kategori" class="form-control" placeholder="Contoh: KAT-04" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Antihistamin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Deskripsi kategori..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3">Simpan Kategori</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>