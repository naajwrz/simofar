<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Apoteker - SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .bg-pink { background-color: #ff85a1; }
        .text-pink { color: #ff85a1; }
        .btn-pink { background-color: #ff85a1; color: white; }
        .btn-pink:hover { background-color: #f76c8c; color: white; }
        .card-menu {
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 133, 161, 0.2) !important;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Apoteker -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-pink shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-capsule-pill me-2"></i>SIMOFAR - Apoteker</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small">Halo, <strong>{{ auth()->user()->nama_staf ?? 'Apoteker' }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold text-dark">Selamat Datang di Panel Apoteker!</h3>
                <p class="text-muted">Kelola resep masuk, pengadaan, dan analisis kesehatan stok obat apotek.</p>
            </div>
        </div>

        <!-- Section Analitik Quick Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                            <i class="bi bi-capsule fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Total Obat</small>
                            <h4 class="fw-bold m-0 text-dark">{{ \App\Models\Obat::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-warning me-3">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Mendekati ED (30hr)</small>
                            <h4 class="fw-bold m-0 text-dark">
                                {{ \App\Models\Obat::where('tanggal_expired', '<=', \Carbon\Carbon::now()->addDays(30))->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3 text-danger me-3">
                            <i class="bi bi-box-arrow-down fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Stok Menipis (< 10)</small>
                            <h4 class="fw-bold m-0 text-dark">
                                {{ \App\Models\Obat::where('stok', '<', 10)->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 text-success me-3">
                            <i class="bi bi-tags fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Kategori Obat</small>
                            <h4 class="fw-bold m-0 text-dark">{{ \App\Models\KategoriObat::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards Menu Utama (3 Kolom Sejajar) -->
        <div class="row g-4 mb-5">
            <!-- Card Stok & Expired -->
            <div class="col-md-4">
                <div class="card card-menu shadow-sm rounded-4 p-4 h-100 border-start border-4 border-warning">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-warning-subtle rounded-3 text-warning me-3">
                            <i class="bi bi-box-seam fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Stok & Kadaluarsa</h5>
                            <small class="text-muted">Cek & edit stok serta ED</small>
                        </div>
                    </div>
                    <p class="text-secondary small">Perbarui stok fisik obat dan pantau peringatan obat yang hampir kadaluarsa.</p>
                    <a href="{{ route('apoteker.obat.index') }}" class="btn btn-pink rounded-3 mt-auto w-100">
                        Kelola Stok & Expired <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card Modul Resep -->
            <div class="col-md-4">
                <div class="card card-menu shadow-sm rounded-4 p-4 h-100 border-start border-4 border-danger">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-danger-subtle rounded-3 text-danger me-3">
                            <i class="bi bi-file-earmark-medical fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Verifikasi Resep</h5>
                            <small class="text-muted">Konfirmasi resep masuk</small>
                        </div>
                    </div>
                    <p class="text-secondary small">Kelola resep yang masuk, verifikasi dosis obat, dan tentukan status kelayakan sebelum diproses.</p>
                    <a href="{{ route('apoteker.resep.index') }}" class="btn btn-pink rounded-3 mt-auto w-100">
                        Buka Modul Resep <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card Modul Pengadaan -->
            <div class="col-md-4">
                <div class="card card-menu shadow-sm rounded-4 p-4 h-100 border-start border-4 border-primary">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-primary-subtle rounded-3 text-primary me-3">
                            <i class="bi bi-truck fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Pengadaan Obat</h5>
                            <small class="text-muted">Pemesanan ke supplier</small>
                        </div>
                    </div>
                    <p class="text-secondary small">Buat transaksi pengadaan ke supplier terdaftar untuk memperbarui jumlah stok obat di sistem.</p>
                    <a href="{{ route('apoteker.pengadaan.index') }}" class="btn btn-pink rounded-3 mt-auto w-100">
                        Buka Modul Pengadaan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Section Grafik Analitik -->
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-line-fill text-pink me-2"></i>Stok Obat Terbanyak</h5>
                    <canvas id="stokChart" height="130"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-pink me-2"></i>Status Kadaluarsa</h5>
                    <canvas id="expiredChart" height="230"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Chart.js -->
    @php
        $topObat = \App\Models\Obat::orderBy('stok', 'desc')->take(5)->get();
        $labelsObat = $topObat->pluck('nama_obat');
        $dataStok = $topObat->pluck('stok');

        $sudahED = \App\Models\Obat::where('tanggal_expired', '<', \Carbon\Carbon::now())->count();
        $hampirED = \App\Models\Obat::whereBetween('tanggal_expired', [\Carbon\Carbon::now(), \Carbon\Carbon::now()->addDays(30)])->count();
        $amanED = \App\Models\Obat::where('tanggal_expired', '>', \Carbon\Carbon::now()->addDays(30))->count();
    @endphp

    <script>
        // Chart Stok Obat
        const ctxStok = document.getElementById('stokChart').getContext('2d');
        new Chart(ctxStok, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsObat) !!},
                datasets: [{
                    label: 'Jumlah Stok',
                    data: {!! json_encode($dataStok) !!},
                    backgroundColor: '#ff85a1',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Chart Status Expired
        const ctxED = document.getElementById('expiredChart').getContext('2d');
        new Chart(ctxED, {
            type: 'doughnut',
            data: {
                labels: ['Aman', 'Mendekati ED', 'Sudah ED'],
                datasets: [{
                    data: [{{ $amanED }}, {{ $hampirED }}, {{ $sudahED }}],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545']
                }]
            },
            options: { responsive: true }
        });
    </script>

    <!-- Footer Section -->
    <footer class="bg-white border-top mt-5 py-4">
        <div class="container">
            <div class="row gy-4">
                <!-- Col 1: Logo & Info Singkat -->
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

                <!-- Col 2: Kontak Admin Dummy -->
                <div class="col-md-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-headset text-pink me-2"></i>Kontak Support & Admin</h6>
                    <ul class="list-unstyled text-muted small lh-lg mb-0">
                        <li><i class="bi bi-envelope me-2 text-pink"></i> admin.simofar@uin-malang.ac.id</li>
                        <li><i class="bi bi-telephone me-2 text-pink"></i> +62 812-3456-7890 (Helpdesk)</li>
                        <li><i class="bi bi-geo-alt me-2 text-pink"></i> Gedung Lab. Komputer, UIN Maulana Malik Ibrahim Malang</li>
                    </ul>
                </div>

                <!-- Col 3: Tim Pengembang -->
                <div class="col-md-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-code-slash text-pink me-2"></i>Tim Tim Pengembang</h6>
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