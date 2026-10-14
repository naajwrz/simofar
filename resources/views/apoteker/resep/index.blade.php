<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Resep - SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .bg-pink { background-color: #ff85a1; }
        .text-pink { color: #ff85a1; }
        .btn-pink { background-color: #ff85a1; color: white; }
        .btn-pink:hover { background-color: #f76c8c; color: white; }
        .btn-outline-pink { border-color: #ff85a1; color: #ff85a1; }
        .btn-outline-pink:hover { background-color: #ff85a1; color: white; }
        .badge-pink { background-color: #ffe6ed; color: #d63384; border: 1px solid #f8cbad; }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Apoteker -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-pink mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('apoteker.dashboard') }}">
                <i class="bi bi-capsule-pill me-2"></i>SIMOFAR - Apoteker
            </a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small"><i class="bi bi-person-circle me-1"></i> {{ auth()->user()->nama_staf ?? 'Apoteker' }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Verifikasi Resep Dokter</h3>
                <p class="text-muted small">Kelola dan verifikasi keabsahan resep pasien sebelum diproses ke kasir</p>
            </div>
            <button type="button"
        class="btn btn-sm"
        style="background-color: #ff85a2; color: white;"
        data-bs-toggle="modal"
        data-bs-target="#modalTambahResep">
    <i class="bi bi-plus-lg"></i> Tambah Resep
</button>
            <a href="{{ route('apoteker.dashboard') }}" class="btn btn-outline-pink btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Alert Notifikasi -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3">
        <strong>Resep belum tersimpan:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        @if($permintaan->isNotEmpty())
<div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-bell text-warning me-1"></i>
        Permintaan Obat Keras dari Pelanggan ({{ $permintaan->count() }})
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pelanggan</th><th>Dokter</th><th>Tgl Resep</th>
                    <th>Obat</th><th>Foto</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permintaan as $p)
                <tr>
                    <td>
                        {{ $p->pelanggan->nama_pelanggan ?? '-' }}
                        <div class="small text-muted">{{ $p->pelanggan->no_telepon ?? '' }}</div>
                    </td>
                    <td>{{ $p->nama_dokter }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_terbit)->format('d/m/Y') }}</td>
                    <td>
                        @foreach($p->detailResep as $d)
                            <strong>{{ $d->obat->nama_obat ?? '-' }}</strong> ({{ $d->jumlah }})
                            <div class="small text-muted">{{ $d->aturan_pakai ?? '-' }}</div>
                        @endforeach
                        @if($p->catatan_pelanggan)
                            <div class="small fst-italic">"{{ $p->catatan_pelanggan }}"</div>
                        @endif
                    </td>
                    <td>
                        @if($p->foto_resep)
                            <a href="{{ asset($p->foto_resep) }}" target="_blank">Lihat foto</a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <form method="POST" action="{{ route('apoteker.resep.acc', $p->id_resep) }}">
                                @csrf
                                <button class="btn btn-sm btn-success rounded-pill px-2">
                                    <i class="bi bi-check-lg"></i> ACC
                                </button>
                            </form>
                            <form method="POST" action="{{ route('apoteker.resep.tolakPermintaan', $p->id_resep) }}"
                                  onsubmit="return confirm('Tolak permintaan ini?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-pink bg-opacity-10 text-pink">
                            <tr>
                                <th class="ps-3 py-3">No. Resep</th>
                                <th class="py-3">Dokter</th>
                                <th class="py-3">Pasien</th>
                                <th class="py-3">Tanggal Terbit</th>
                                <th class="py-3">Rincian Obat & Aturan Pakai</th>
                                <th class="py-3">Status Validasi</th>
                                <th class="text-center pe-3 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resepList as $resep)
                                <tr>
                                    <td class="ps-3 fw-bold text-pink">{{ $resep->no_resep }}</td>
                                    <td>{{ $resep->nama_dokter }}</td>
                                    <td>{{ $resep->pelanggan->nama_pelanggan ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($resep->tanggal_terbit)->format('d/m/Y') }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($resep->detailResep as $detail)
                                                <li class="mb-1">
                                                    <i class="bi bi-capsule text-pink me-1"></i>
                                                    <strong>{{ $detail->obat->nama_obat ?? 'Obat Tidak Ditemukan' }}</strong> 
                                                    ({{ $detail->jumlah }} {{ $detail->obat->satuan ?? 'pcs' }})
                                                    <br>
                                                    <span class="text-muted ms-3"><em>Aturan: {{ $detail->aturan_pakai ?? '-' }}</em></span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if($resep->status_validasi === 'valid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Valid</span>
                                        @elseif($resep->status_validasi === 'tidak_valid')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Tidak Valid</span>
                                        @elseif($resep->status_validasi === 'terpakai')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">Terpakai</span>
                                        @else
                                            <span class="badge badge-pink px-2 py-1 rounded-pill">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        @if($resep->status_validasi === 'menunggu')
                                            <div class="d-flex justify-content-center gap-1">
                                                <form action="{{ route('apoteker.resep.verifikasi', $resep->id_resep) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_validasi" value="valid">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Setujui Resep" onclick="return confirm('Apakah Anda yakin meresepkan obat ini sudah sesuai?')">
                                                        <i class="bi bi-check-lg"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('apoteker.resep.verifikasi', $resep->id_resep) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_validasi" value="tidak_valid">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Tolak Resep" onclick="return confirm('Tolak resep ini?')">
                                                        <i class="bi bi-x-lg"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="bi bi-lock-fill me-1"></i>Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 text-pink d-block mb-2"></i>
                                        Belum ada data resep yang dikirim.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
    <!-- Modal Tambah Resep -->
<div class="modal fade" id="modalTambahResep" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header"
                 style="background-color: #ff85a2; color: white;">

                <h5 class="modal-title">
                    <i class="bi bi-prescription2 me-2"></i>
                    Tambah Resep Dokter
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form action="{{ route('apoteker.resep.store') }}" method="POST">

                @csrf

                <div class="modal-body">

                    <!-- Data Resep -->
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                No. Resep
                            </label>

                            <input type="text"
                                   name="no_resep"
                                   class="form-control"
                                   value="{{ old('no_resep', $noResepBaru) }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Terbit
                            </label>

                            <input type="date"
                                   name="tanggal_terbit"
                                   class="form-control"
                                   value="{{ date('Y-m-d') }}"
                                   required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Pasien
                            </label>

                            <div class="input-group">
                                <select name="id_pelanggan"
                                        id="pasienResep"
                                        class="form-select"
                                        data-pasien-url="{{ route('apoteker.resep.pasien') }}"
                                        required>
                                    <option value="">-- Pilih Pasien --</option>
                                    @foreach($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id_pelanggan }}"
                                            @selected(old('id_pelanggan') == $pelanggan->id_pelanggan)>
                                            {{ $pelanggan->nama_pelanggan }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary"
                                        id="refreshPasien" title="Muat ulang daftar pasien"
                                        aria-label="Muat ulang daftar pasien">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div id="statusPasien" class="form-text" aria-live="polite">
                                Daftar pasien diperbarui saat formulir dibuka.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Nama Dokter
                            </label>

                            <input type="text"
                                   name="nama_dokter"
                                   class="form-control"
                                   placeholder="Contoh: dr. Rina Lestari"
                                   required>
                        </div>

                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-capsule me-1"></i>
                        Obat dalam Resep
                    </h6>

                    <div id="daftarObat">

                        <!-- Baris obat pertama -->
                        <div class="row obat-row mb-3">

                            <div class="col-md-5">
                                <label class="form-label">
                                    Obat
                                </label>

                                <select name="obat[0][id_obat]"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Obat --
                                    </option>

                                    @foreach($obats as $obat)

                                        <option value="{{ $obat->id_obat }}">
                                            {{ $obat->nama_obat }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Jumlah
                                </label>

                                <input type="number"
                                       name="obat[0][jumlah]"
                                       class="form-control"
                                       min="1"
                                       value="1"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Aturan Pakai
                                </label>

                                <input type="text"
                                       name="obat[0][aturan_pakai]"
                                       class="form-control"
                                       placeholder="Contoh: 3 x 1 sesudah makan">
                            </div>

                            <div class="col-md-1 d-flex align-items-end">

                                <button type="button"
                                        class="btn btn-outline-danger btn-remove-obat"
                                        style="display:none;">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                    <button type="button"
                            id="btnTambahObat"
                            class="btn btn-sm"
                            style="border: 1px solid #ff85a2; color: #ff85a2;">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Obat
                    </button>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn"
                            style="background-color: #ff85a2; color:white;">
                        <i class="bi bi-save me-1"></i>
                        Simpan Resep
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
<script>
    let obatIndex = 1;

    const pasienSelect = document.getElementById('pasienResep');
    const refreshPasienButton = document.getElementById('refreshPasien');
    const statusPasien = document.getElementById('statusPasien');
    const modalTambahResep = document.getElementById('modalTambahResep');
    let pasienRefreshTimer;

    async function refreshDaftarPasien() {
        if (refreshPasienButton.disabled) {
            return;
        }

        const selectedPasien = pasienSelect.value;
        refreshPasienButton.disabled = true;
        refreshPasienButton.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';
        statusPasien.textContent = 'Memuat daftar pasien...';
        statusPasien.classList.remove('text-danger');

        try {
            const response = await fetch(pasienSelect.dataset.pasienUrl, {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error('Daftar pasien gagal dimuat. Coba muat ulang lagi.');
            }

            const pelanggans = await response.json();
            pasienSelect.replaceChildren(new Option('-- Pilih Pasien --', ''));

            pelanggans.forEach(pelanggan => {
                pasienSelect.add(new Option(pelanggan.nama_pelanggan, pelanggan.id_pelanggan));
            });

            if (pelanggans.some(pelanggan => String(pelanggan.id_pelanggan) === selectedPasien)) {
                pasienSelect.value = selectedPasien;
            }

            statusPasien.textContent = `${pelanggans.length} pasien aktif. Daftar diperbarui.`;
            statusPasien.classList.remove('text-danger');
        } catch (error) {
            statusPasien.textContent = `Gagal memuat daftar pasien: ${error.message}`;
            statusPasien.classList.add('text-danger');
        } finally {
            refreshPasienButton.disabled = false;
            refreshPasienButton.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        }
    }

    refreshPasienButton.addEventListener('click', refreshDaftarPasien);
    modalTambahResep.addEventListener('show.bs.modal', refreshDaftarPasien);
    modalTambahResep.addEventListener('shown.bs.modal', () => {
        pasienRefreshTimer = window.setInterval(refreshDaftarPasien, 15000);
    });
    modalTambahResep.addEventListener('hidden.bs.modal', () => {
        window.clearInterval(pasienRefreshTimer);
    });

    document.getElementById('btnTambahObat').addEventListener('click', function () {

        const daftarObat = document.getElementById('daftarObat');

        const row = document.createElement('div');

        row.classList.add('row', 'obat-row', 'mb-3');

        row.innerHTML = `
            <div class="col-md-5">
                <select name="obat[${obatIndex}][id_obat]"
                        class="form-select"
                        required>

                    <option value="">
                        -- Pilih Obat --
                    </option>

                    @foreach($obats as $obat)
                        <option value="{{ $obat->id_obat }}">
                            {{ $obat->nama_obat }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-2">
                <input type="number"
                       name="obat[${obatIndex}][jumlah]"
                       class="form-control"
                       min="1"
                       value="1"
                       required>
            </div>

            <div class="col-md-4">
                <input type="text"
                       name="obat[${obatIndex}][aturan_pakai]"
                       class="form-control"
                       placeholder="Contoh: 3 x 1 sesudah makan">
            </div>

            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-remove-obat">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        daftarObat.appendChild(row);

        obatIndex++;
    });


    document.addEventListener('click', function (e) {

        if (e.target.closest('.btn-remove-obat')) {

            const row = e.target.closest('.obat-row');

            row.remove();

        }

    });

    @if($errors->any() || session('error'))
    window.addEventListener('load', () => new bootstrap.Modal(document.getElementById('modalTambahResep')).show());
@endif
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>