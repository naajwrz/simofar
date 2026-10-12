<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        html { scroll-behavior: smooth; }

        .text-pink { color: #ff85a1; }
        .bg-pink { background-color: #ff85a1; }
        .btn-pink { background-color: #ff85a1; border-color: #ff85a1; color: white; }
        .btn-pink:hover { background-color: #f76c8c; border-color: #f76c8c; color: white; }

        /* ===== Hero: kotak login berbayangan ===== */
        .hero {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem 1.5rem;
            background: linear-gradient(135deg, #fde2e4 0%, #ffcad4 100%);
        }
        .hero::before, .hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .55;
        }
        .hero::before { width: 380px; height: 380px; background: #ff85a1; top: -130px; left: -110px; }
        .hero::after { width: 320px; height: 320px; background: #ffb3c6; bottom: -110px; right: -90px; }
        .hero > * { position: relative; z-index: 1; }

        .login-card {
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 22px;
            width: 100%;
            max-width: 430px;
            background-color: #ffffff;
            overflow: hidden;
            box-shadow:
                0 30px 60px rgba(217, 108, 145, .35),
                0 10px 20px rgba(73, 54, 64, .10);
        }

        .scroll-hint {
            margin-top: 1.75rem;
            color: #b9577a;
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            text-align: center;
        }
        .scroll-hint:hover { color: #8f3d5c; }
        .scroll-hint i { display: block; font-size: 1.3rem; animation: goyang 1.6s ease-in-out infinite; }
        @keyframes goyang {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(6px); }
        }

        .nav-pills .nav-link { color: #6c757d; font-weight: 600; border-radius: 10px; }
        .nav-pills .nav-link.active { background-color: #ff85a1; color: white; }
        .form-control:focus {
            border-color: #ff85a1;
            box-shadow: 0 0 0 0.25rem rgba(255, 133, 161, 0.25);
        }

        /* ===== Profil ===== */
        .tentang { background: #fff8fa; }
        .fitur-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(244, 162, 188, .18);
            height: 100%;
        }
        .fitur-ikon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            background: #ffe6ed; color: #d63384;
        }
    </style>
</head>
<body>

<!-- ================= HERO / LOGIN ================= -->
<header class="hero">
    <div class="card login-card p-4">
        <div class="card-body">
            <div class="text-center mb-3">
                <h3 class="fw-bold text-pink"><i class="bi bi-capsule-pill me-2"></i>SIMOFAR</h3>
                <p class="text-muted small">Sistem Manajemen Obat dan Farmasi</p>
            </div>

            <!-- 2 Tab Navigasi: Kiri Staf, Kanan Pelanggan -->
            <ul class="nav nav-pills nav-justified mb-4 bg-light p-1 rounded-3" id="loginTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="apoteker-tab" data-bs-toggle="pill" data-bs-target="#apoteker-panel" type="button" role="tab">
                        <i class="bi bi-person-badge me-1"></i> Staf
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pelanggan-tab" data-bs-toggle="pill" data-bs-target="#pelanggan-panel" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Pelanggan
                    </button>
                </li>
            </ul>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success small">{{ session('success') }}</div>
            @endif

            <div class="tab-content" id="loginTabContent">
                <!-- Panel 1: Staf (Admin / Apoteker) -->
                <div class="tab-pane fade show active" id="apoteker-panel" role="tabpanel">
                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_type" value="apoteker">
                        <div class="mb-3">
                            <label for="email_apoteker" class="form-label font-semibold text-secondary">Email Staf</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-pink"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email_apoteker" class="form-control" placeholder="nama@farmasi.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pass_apoteker" class="form-label font-semibold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-pink"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="pass_apoteker" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold rounded-3 shadow-sm mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </button>

                        <div class="text-center">
                            <small class="text-muted">Belum punya akun staf? <br><strong class="text-pink">Silakan hubungi Admin.</strong></small>
                        </div>
                    </form>
                </div>

                <!-- Panel 2: Pelanggan / Pembeli -->
                <div class="tab-pane fade" id="pelanggan-panel" role="tabpanel">
                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_type" value="pelanggan">
                        <div class="mb-3">
                            <label for="email_pelanggan" class="form-label font-semibold text-secondary">Email Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-pink"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email_pelanggan" class="form-control" placeholder="pelanggan@gmail.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pass_pelanggan" class="form-label font-semibold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-pink"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="pass_pelanggan" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold rounded-3 shadow-sm mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </button>

                        <div class="text-center">
                            <small class="text-muted">Belum punya akun pelanggan?
                                <a href="{{ route('register') }}" class="text-pink fw-semibold text-decoration-none">Daftar Sekarang</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="#tentang" class="scroll-hint">
        Tentang SIMOFAR
        <i class="bi bi-chevron-double-down"></i>
    </a>
</header>

<!-- ================= PROFIL SIMOFAR ================= -->
<section id="tentang" class="tentang py-5">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge rounded-pill px-3 py-2" style="background:#ffe6ed;color:#d63384;">Tentang Kami</span>
            <h2 class="fw-bold mt-3">Profil SIMOFAR</h2>
            <p class="text-muted mx-auto mb-0" style="max-width: 680px;">
                SIMOFAR (Sistem Informasi Manajemen Obat &amp; Farmasi Terpadu) membantu apotek mengelola stok obat,
                resep dokter, pengadaan, dan transaksi penjualan dalam satu aplikasi, sehingga data selalu
                akurat dan mudah dipantau.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card fitur-card p-3">
                    <div class="fitur-ikon mb-3"><i class="bi bi-box-seam"></i></div>
                    <h6 class="fw-bold">Manajemen Stok</h6>
                    <p class="text-muted small mb-0">Pantau stok, harga, dan tanggal kedaluwarsa obat sehingga persediaan selalu terkendali.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card fitur-card p-3">
                    <div class="fitur-ikon mb-3"><i class="bi bi-file-earmark-medical"></i></div>
                    <h6 class="fw-bold">Verifikasi Resep</h6>
                    <p class="text-muted small mb-0">Pelanggan mengajukan obat keras, lalu apoteker memeriksa dan memverifikasi resep dokter.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card fitur-card p-3">
                    <div class="fitur-ikon mb-3"><i class="bi bi-cart-check"></i></div>
                    <h6 class="fw-bold">Katalog &amp; Pembayaran</h6>
                    <p class="text-muted small mb-0">Pelanggan melihat detail obat per kategori, checkout, dan membayar langsung dari katalog.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card fitur-card p-3">
                    <div class="fitur-ikon mb-3"><i class="bi bi-bar-chart-line"></i></div>
                    <h6 class="fw-bold">Dashboard &amp; Laporan</h6>
                    <p class="text-muted small mb-0">Ringkasan penjualan dan kondisi bisnis apotek untuk membantu pengambilan keputusan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-white border-top py-4">
    <div class="container">
        <div class="row gy-4">
            <!-- Col 1: Logo & Info Singkat -->
            <div class="col-md-5">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-capsule-pill fs-3 text-pink me-2"></i>
                    <h5 class="fw-bold mb-0 text-dark">SIMOFAR</h5>
                </div>
                <p class="text-muted small mb-2">
                    Sistem Informasi Manajemen Obat &amp; Farmasi Terpadu untuk mengoptimalkan pengelolaan stok, resep, dan pengadaan obat secara akurat.
                </p>
                <small class="text-muted d-block">&copy; 2026 SIMOFAR. All Rights Reserved.</small>
            </div>

            <!-- Col 2: Kontak -->
            <div class="col-md-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-headset text-pink me-2"></i>Kontak Support &amp; Admin</h6>
                <ul class="list-unstyled text-muted small lh-lg mb-0">
                    <li><i class="bi bi-envelope me-2 text-pink"></i> admin.simofar@uin-malang.ac.id</li>
                    <li><i class="bi bi-telephone me-2 text-pink"></i> +62 812-3456-7890 (Helpdesk)</li>
                    <li><i class="bi bi-geo-alt me-2 text-pink"></i> Gedung Lab. Komputer, UIN Maulana Malik Ibrahim Malang</li>
                </ul>
            </div>

            <!-- Col 3: Tim Pengembang -->
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