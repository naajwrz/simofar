
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Obat | SIMOFAR Pharmacy</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
       
:root {
    --primary: #F4A6BB;
    --primary-dark: #D96C91;
    --soft: #FFF0F5;
    --ink: #493640;
    --muted: #9A858F;
}

body {
    background: #FFFAFC;
    color: var(--ink);
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
}

.navbar {
    background: #FFFFFF;
    border-bottom: 1px solid #F9E5EC;
}

.brand-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    color: white;
    background: var(--primary);
    font-size: 22px;
}

.text-primary-custom {
    color: var(--primary-dark) !important;
}

.btn-primary-custom {
    background: var(--primary);
    color: white;
    border: 1px solid var(--primary);
    border-radius: 10px;
    transition: all .2s ease;
}

.btn-primary-custom:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    color: white;
    transform: translateY(-1px);
}

.hero {
    background: linear-gradient(120deg, #FFE6EE, #FFF2F7, #FFF8F4);
    border: 1px solid #F9D8E3;
    border-radius: 25px;
    padding: 36px;
}

.hero-icon {
    width: 110px;
    height: 110px;
    border-radius: 28px;
    background: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 58px;
    color: var(--primary-dark);
    box-shadow: 0 12px 30px #D96C911A;
}

.search-box,
.catalog-card {
    border: 1px solid #F5E5EB;
    border-radius: 18px;
    background: #FFFFFF;
}

.catalog-card {
    height: 100%;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}

.catalog-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px #D96C911A;
}

.medicine-art {
    height: 150px;
    background: linear-gradient(135deg, #FFF0F5, #FFF7F9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-dark);
    font-size: 56px;
}

.medicine-name {
    min-height: 48px;
    font-size: 17px;
    font-weight: 700;
}

.badge-soft {
    background: #FFE5EE;
    color: #B84F76;
}

.price {
    font-size: 19px;
    font-weight: 800;
    color: #D96C91;
}

.muted {
    color: var(--muted);
}

.cart-count {
    min-width: 22px;
    height: 22px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #E87598;
    color: white;
    font-size: 12px;
    padding: 0 5px;
}

.empty-state {
    border: 1px dashed #EBC6D4;
    border-radius: 18px;
    background: #FFFFFF;
}

.cart-item {
    border-bottom: 1px solid #F5E5EB;
    padding: 14px 0;
}

footer {
    background: #FFFFFF;
    border-top: 1px solid #F9E5EC;
}

@media (max-width: 576px) {
    .hero {
        padding: 24px;
    }

    .hero-icon {
        display: none;
    }
}

    </style>
</head>

<body>

<nav class="navbar sticky-top">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('katalog.index') }}">
            <span class="brand-icon"><i class="bi bi-capsule-pill"></i></span>
            <span>
                <strong class="d-block">SIMOFAR</strong>
                <small class="muted">Pharmacy</small>
            </span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary position-relative rounded-pill px-3"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
                <i class="bi bi-bag me-1"></i>
                <span class="d-none d-sm-inline">Keranjang</span>
                <span class="cart-count ms-1" id="cartCount">0</span>
            </button>

           <a href="{{ route('login') }}" class="btn btn-primary-custom rounded-pill px-3">
                <i class="bi bi-person me-1"></i>
                <span class="d-none d-sm-inline">Login Staf</span>
                <span class="d-sm-none">Login</span>
            </a>
            --> <!-- (Bagian lama di atas sudah diganti dengan kode di bawah ini) -->

            @auth
                @if(auth()->user()->isPelanggan())
                    <a href="{{ route('pelanggan.permintaan.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-file-earmark-medical me-1"></i>
                        <span class="d-none d-sm-inline">Permintaan Saya</span>
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-3">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary-custom rounded-pill px-3">
                    <i class="bi bi-person me-1"></i>
                    <span class="d-none d-sm-inline">Login Staf</span>
                    <span class="d-sm-none">Login</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="hero mb-4 mb-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">
                    <i class="bi bi-heart-pulse me-1"></i> Layanan Apotek SIMOFAR
                </span>

                <h1 class="display-6 fw-bold mb-3">
                    Temukan kebutuhan kesehatanmu.
                </h1>

                <p class="muted mb-4">
                    Jelajahi katalog obat, cek informasi produk, dan masukkan
                    obat yang sesuai ke keranjangmu.
                </p>

                <a href="#katalog" class="btn btn-primary-custom rounded-pill px-4 py-2">
                    Jelajahi Katalog <i class="bi bi-arrow-down ms-1"></i>
                </a>
            </div>

            <div class="col-md-4 d-flex justify-content-md-end">
                <div class="hero-icon">
                    <i class="bi bi-prescription2"></i>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h3 fw-bold mb-1">Katalog Obat</h2>
                <p class="muted mb-0">Cari produk berdasarkan nama atau kategori.</p>
            </div>

            <span class="badge text-bg-light border rounded-pill px-3 py-2">
                {{ $obats->count() }} produk tersedia
            </span>
        </div>

        <div class="search-box p-3 mb-4">
            <form action="{{ route('katalog.index') }}" method="GET" class="row g-2">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama obat..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                <div class="col-lg-4">
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua kategori</option>
                        @foreach($kategoris as $k)
                            <option
                                value="{{ $k->id_kategori }}"
                                {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}
                            >
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 d-grid">
                    <button type="submit" class="btn btn-primary-custom">
                        Cari Obat
                    </button>
                </div>
            </form>
            <div class="d-flex flex-wrap gap-2 mt-3">
    <a href="{{ route('katalog.index', request()->except('kategori')) }}"
       class="btn btn-sm rounded-pill {{ request('kategori') ? 'btn-outline-secondary' : 'btn-primary-custom' }}">
        Semua
    </a>
    @foreach($kategoris as $k)
        <a href="{{ route('katalog.index', array_merge(request()->except('kategori'), ['kategori' => $k->id_kategori])) }}"
           class="btn btn-sm rounded-pill {{ request('kategori') == $k->id_kategori ? 'btn-primary-custom' : 'btn-outline-secondary' }}">
            {{ $k->nama_kategori }}
        </a>
    @endforeach
</div>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse($obats as $o)
                @php
                    $golongan = $o->golongan ?? 'bebas';
                    $obatKeras = $golongan === 'keras';
                @endphp

                <div class="col-6 col-md-4 col-xl-3">
                    <article class="catalog-card">
                        <div class="medicine-art">
                            <i class="bi bi-capsule"></i>
                        </div>

                        <div class="p-3">
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <span class="badge badge-soft">
                                    {{ $o->kategori->nama_kategori ?? 'Umum' }}
                                </span>

                                @if($obatKeras)
                                    <span class="badge text-bg-danger">Obat keras</span>
                                @elseif($golongan === 'bebas_terbatas')
                                    <span class="badge text-bg-warning">Bebas terbatas</span>
                                @else
                                    <span class="badge text-bg-success">Obat bebas</span>
                                @endif
                            </div>

                            <h3 class="medicine-name">
                                {{ $o->nama_obat }}
                            </h3>

                            <p class="small muted mb-3">
                                <i class="bi bi-box-seam me-1"></i>
                                {{ $o->satuan }} · Stok {{ $o->stok }}
                            </p>

                            <div class="price mb-3">
                                Rp{{ number_format($o->harga_jual, 0, ',', '.') }}
                            </div>

                            <div class="d-grid gap-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal"
                                    data-id="{{ $o->id_obat }}"
                                    data-name="{{ $o->nama_obat }}"
                                    data-category="{{ $o->kategori->nama_kategori ?? 'Umum' }}"
                                    data-golongan="{{ str_replace('_', ' ', $golongan) }}"
                                    data-price="{{ $o->harga_jual }}"
                                    data-stock="{{ $o->stok }}"
                                    data-unit="{{ $o->satuan }}"
                                    data-expired="{{ $o->tanggal_expired ?? 'Tidak tersedia' }}"
                                    data-description="{{ $o->getAttribute('deskripsi') ?? $o->getAttribute('keterangan') ?? '' }}"
                                    data-indikasi="{{ $o->getAttribute('indikasi') ?? '' }}"
                                    data-aturan="{{ $o->getAttribute('aturan_pakai') ?? $o->getAttribute('aturan_penggunaan') ?? '' }}"
                                    data-efek="{{ $o->getAttribute('efek_samping') ?? '' }}"
                                    data-kontra="{{ $o->getAttribute('kontraindikasi') ?? '' }}"
                                    data-catatan-apoteker="{{ $o->getAttribute('catatan_apoteker') ?? $o->getAttribute('informasi_apoteker') ?? '' }}"
                                >
                                    <i class="bi bi-eye me-1"></i> Detail Obat
                                </button>

                                @if($obatKeras)
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#prescriptionModal"
                                        data-name="{{ $o->nama_obat }}"
                                    >
                                        <i class="bi bi-file-earmark-medical me-1"></i>
                                        Perlu Resep
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-primary-custom btn-sm add-to-cart"
                                        data-id="{{ $o->id_obat }}"
                                        data-name="{{ $o->nama_obat }}"
                                        data-price="{{ $o->harga_jual }}"
                                        data-stock="{{ $o->stok }}"
                                        data-unit="{{ $o->satuan }}"
                                    >
                                        <i class="bi bi-bag-plus me-1"></i>
                                        Tambah ke Keranjang
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state text-center p-5">
                        <i class="bi bi-search display-4 muted"></i>
                        <h3 class="h5 fw-bold mt-3">Obat tidak ditemukan</h3>
                        <p class="muted">Coba gunakan kata kunci atau kategori lain.</p>
                        <a href="{{ route('katalog.index') }}"
                           class="btn btn-primary-custom rounded-pill px-4">
                            Reset Pencarian
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</main>

<footer class="border-top mt-5 py-4">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <strong><i class="bi bi-capsule-pill text-primary-custom me-1"></i> SIMOFAR Pharmacy</strong>
        <small class="muted">Informasi obat dan layanan kesehatan.</small>
    </div>
</footer>

<!-- Detail Obat -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0">
                <h2 class="modal-title fs-5 fw-bold">Detail Obat</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="medicine-art rounded-4 mb-3">
                    <i class="bi bi-capsule-pill"></i>
                </div>

                <h3 class="h4 fw-bold" id="detailName">-</h3>
                <p class="muted mb-3" id="detailCategory">-</p>

                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="muted">Golongan</span>
                    <strong class="text-capitalize" id="detailGolongan">-</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="muted">Stok</span>
                    <strong id="detailStock">-</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="muted">Kedaluwarsa</span>
                    <strong id="detailExpired">-</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <span class="muted">Harga</span>
                    <strong class="price" id="detailPrice">-</strong>
                </div>

                <div class="mt-4 p-3 rounded-3" style="background:#FFF6F9;border:1px solid #F5E5EB">
                    <h4 class="h6 fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary-custom"></i>Informasi dari Apoteker</h4>
                    <div class="mb-2" id="detailDescriptionRow">
                        <div class="small muted">Deskripsi obat</div>
                        <div id="detailDescription">Belum ada informasi.</div>
                    </div>
                    <div class="mb-2" id="detailIndikasiRow">
                        <div class="small muted">Indikasi / kegunaan</div>
                        <div id="detailIndikasi">Belum ada informasi.</div>
                    </div>
                    <div class="mb-2" id="detailAturanRow">
                        <div class="small muted">Aturan pakai</div>
                        <div id="detailAturan">Belum ada informasi.</div>
                    </div>
                    <div class="mb-2" id="detailEfekRow">
                        <div class="small muted">Efek samping</div>
                        <div id="detailEfek">Belum ada informasi.</div>
                    </div>
                    <div class="mb-2" id="detailKontraRow">
                        <div class="small muted">Kontraindikasi / perhatian</div>
                        <div id="detailKontra">Belum ada informasi.</div>
                    </div>
                    <div id="detailCatatanApotekerRow">
                        <div class="small muted">Catatan apoteker</div>
                        <div id="detailCatatanApoteker">Belum ada informasi.</div>
                    </div>
                </div>

                <p class="small muted mt-3 mb-0">
                    Informasi ini bukan pengganti konsultasi dengan apoteker.
                    Ikuti petunjuk pada kemasan dan gunakan obat sesuai arahan tenaga kesehatan.
                </p>
            </div>
        </div>
    </div>
</div>

data-id="{{ $o->id_obat }}"

<!-- Keranjang -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartPanel"
     aria-labelledby="cartTitle">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title fs-5 fw-bold" id="cartTitle">
            <i class="bi bi-bag me-2"></i>Keranjang Belanja
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                aria-label="Tutup"></button>
    </div>

    <div class="offcanvas-body">
        <div id="cartItems"></div>

        <div id="cartSummary" class="border-top pt-3 mt-3 d-none">
            <div class="d-flex justify-content-between mb-3">
                <span class="muted">Total sementara</span>
                <strong class="price" id="cartTotal">Rp0</strong>
            </div>

            <button type="button" class="btn btn-primary-custom w-100"
                    id="checkoutButton">
                Lanjutkan Pemesanan
            </button>

            <p class="small muted mt-2 mb-0">
                Keranjang ini masih tersimpan di browser. Pemesanan belum dikirim ke server.
            </p>
        </div>
    </div>
</div>

<!-- Modal Checkout -->
<div class="modal fade" id="checkoutModal" tabindex="-1"
     aria-labelledby="checkoutModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="checkoutModalTitle">
                        Data Pelanggan
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary">
                        Isi data untuk melanjutkan checkout QRIS simulasi.
                    </p>
                    <div class="mb-3">
                        <label for="namaPelanggan" class="form-label">Nama lengkap</label>
                        <input class="form-control" id="namaPelanggan"
                               name="nama_pelanggan" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="noTelepon" class="form-label">Nomor HP</label>
                        <input class="form-control" id="noTelepon"
                               name="no_telepon" type="tel"
                               maxlength="15" required>
                    </div>
                    <div id="checkoutHiddenItems"></div>
                    <div class="alert alert-light border small mb-0">
                        Total final akan dihitung ulang oleh server berdasarkan harga dan stok terbaru.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom"
                            id="submitCheckout">
                        Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const CART_KEY = 'simofar_cart_v1';
    const rupiah = value => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(value);

    function readCart() {
        try {
            const cart = JSON.parse(localStorage.getItem(CART_KEY) || '[]');
            return Array.isArray(cart) ? cart : [];
        } catch {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        renderCart();
    }

   
function renderCart() {
    const cart = readCart();

    const count = cart.reduce(
        (sum, item) => sum + Number(item.qty || 0), 0
    );

    const total = cart.reduce(
        (sum, item) =>
            sum + Number(item.price || 0) * Number(item.qty || 0), 0
    );

    const countEl = document.getElementById('cartCount');
    const totalEl = document.getElementById('cartTotal');
    const itemsEl = document.getElementById('cartItems');
    const summaryEl = document.getElementById('cartSummary');

    if (!countEl || !totalEl || !itemsEl || !summaryEl) {
        console.error('Elemen HTML keranjang tidak lengkap.');
        return;
    }

    countEl.textContent = count;
    totalEl.textContent = rupiah(total);

    if (cart.length === 0) {
        itemsEl.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-bag display-4 muted"></i>
                <h3 class="h6 fw-bold mt-3">Keranjang masih kosong</h3>
                <p class="small muted">
                    Pilih obat dari katalog terlebih dahulu.
                </p>
            </div>`;
        summaryEl.classList.add('d-none');
        return;
    }

    summaryEl.classList.remove('d-none');

    itemsEl.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="d-flex justify-content-between gap-2">
                <div>
                    <strong>${escapeHtml(item.name)}</strong>
                    <div class="small muted">
                        ${rupiah(item.price)} / ${escapeHtml(item.unit || '')}
                    </div>
                </div>
                <button type="button"
                    class="btn btn-sm btn-link text-danger remove-item"
                    data-id="${Number(item.id)}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary qty-change"
                        data-id="${Number(item.id)}" data-change="-1">−</button>
                    <button class="btn btn-light" disabled>
                        ${Number(item.qty)}
                    </button>
                    <button class="btn btn-outline-secondary qty-change"
                        data-id="${Number(item.id)}" data-change="1"
                        ${Number(item.qty) >= Number(item.stock) ? 'disabled' : ''}>+</button>
                </div>
                <strong>${rupiah(Number(item.price) * Number(item.qty))}</strong>
            </div>
        </div>
    `).join('');
}


    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);
    }

    
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', () => {
        const id = Number(button.dataset.id);
        const stock = Number(button.dataset.stock);
        const price = Number(button.dataset.price);
        const cart = readCart();
        const existing = cart.find(item => item.id === id);

        if (stock < 1) {
            alert('Stok obat sedang kosong.');
            return;
        }

        if (existing) {
            if (existing.qty >= stock) {
                alert('Jumlah di keranjang sudah mencapai stok yang tersedia.');
                return;
            }

            existing.qty++;
        } else {
            cart.push({
                id: id,
                name: button.dataset.name,
                price: price,
                stock: stock,
                unit: button.dataset.unit,
                qty: 1
            });
        }

        saveCart(cart);
        renderCart();

        button.innerHTML =
            '<i class="bi bi-check2 me-1"></i> Ditambahkan';

        setTimeout(() => {
            button.innerHTML =
                '<i class="bi bi-bag-plus me-1"></i> Tambah ke Keranjang';
        }, 1000);
    });
});

    document.getElementById('cartItems').addEventListener('click', event => {
        const removeButton = event.target.closest('.remove-item');
        const changeButton = event.target.closest('.qty-change');
        let cart = readCart();

        if (removeButton) {
            cart = cart.filter(item => item.id !== Number(removeButton.dataset.id));
            saveCart(cart);
            return;
        }

        if (changeButton) {
            const id = Number(changeButton.dataset.id);
            const change = Number(changeButton.dataset.change);
            const item = cart.find(product => product.id === id);

            if (!item) return;

            item.qty += change;

            if (item.qty <= 0) {
                cart = cart.filter(product => product.id !== id);
            } else if (item.qty > item.stock) {
                item.qty = item.stock;
            }

            saveCart(cart);
        }
    });

    
document.getElementById('checkoutButton').addEventListener('click', () => {
    const cart = readCart();

    if (!cart.length) {
        alert('Keranjang masih kosong.');
        return;
    }

    const hiddenItems = document.getElementById('checkoutHiddenItems');
    hiddenItems.innerHTML = '';

    cart.forEach((item, index) => {
        const id = document.createElement('input');
        id.type = 'hidden';
        id.name = `items[${index}][id]`;
        id.value = item.id;

        const qty = document.createElement('input');
        qty.type = 'hidden';
        qty.name = `items[${index}][qty]`;
        qty.value = item.qty;

        hiddenItems.append(id, qty);
    });

    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('checkoutModal')
    ).show();
});

document.getElementById('checkoutForm').addEventListener('submit', () => {
    const button = document.getElementById('submitCheckout');
    button.disabled = true;
    button.textContent = 'Memproses pesanan...';
});


    document.getElementById('detailModal').addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('detailName').textContent = button.dataset.name;
        document.getElementById('detailCategory').textContent = button.dataset.category;
        document.getElementById('detailGolongan').textContent = button.dataset.golongan;
        document.getElementById('detailStock').textContent =
            `${button.dataset.stock} ${button.dataset.unit}`;
        document.getElementById('detailExpired').textContent = button.dataset.expired;
        document.getElementById('detailPrice').textContent =
            rupiah(Number(button.dataset.price));

        const infoFields = [
            ['Description', 'description'],
            ['Indikasi', 'indikasi'],
            ['Aturan', 'aturan'],
            ['Efek', 'efek'],
            ['Kontra', 'kontra'],
            ['CatatanApoteker', 'catatanApoteker']
        ];
        infoFields.forEach(([label, key]) => {
            const value = (button.dataset[key] || '').trim();
            const target = document.getElementById('detail' + label);
            const row = document.getElementById('detail' + label + 'Row');
            if (target) target.textContent = value || 'Belum ada informasi dari apoteker.';
            if (row) row.classList.remove('d-none');
        });
        const infoContainer = document.getElementById('detailDescription').closest('.p-3');
        infoContainer.classList.remove('d-none');
    });

   document.getElementById('prescriptionModal').addEventListener('show.bs.modal', event => {
    const tombol = event.relatedTarget;
    document.getElementById('prescriptionName').textContent = tombol.dataset.name;
    const idInput = document.getElementById('prescriptionId');
    if (idInput) idInput.value = tombol.dataset.id;
});
    // Muat ulang saat halaman dipulihkan lewat tombol Back, supaya stok dan keranjang terbaru.
window.addEventListener('pageshow', event => {
    if (event.persisted) window.location.reload();
});

    renderCart();
    
</script>

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
