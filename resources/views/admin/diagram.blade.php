@extends('admin.layout')
@section('title', 'Diagram Sistem')

@section('content')
<h3 class="fw-bold mb-1">Diagram Sistem Informasi</h3>
<p class="text-muted small mb-4">Gambaran alur dan struktur data SIMOFAR.</p>

@verbatim
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Alur Sistem</h6>
        <div class="overflow-auto">
<pre class="mermaid">
flowchart LR
    P["Pelanggan"]
    A["Apoteker"]
    AD["Admin"]
    K["Katalog dan Detail Obat"]
    T["Checkout dan Pembayaran QRIS"]
    R["Permintaan Obat Keras"]
    V["Verifikasi Resep Dokter"]
    S[("Data Obat dan Stok")]
    PG["Pengadaan dari Supplier"]
    D["Dashboard Bisnis"]
    U["Kelola Akun Apoteker dan Pelanggan"]

    P -->|"melihat obat sesuai kategori"| K
    K -->|"obat bebas"| T
    K -->|"obat keras"| R
    T -->|"stok berkurang setelah bayar"| S
    R -->|"ACC apoteker"| V
    A -->|"periksa permintaan"| R
    A -->|"setujui atau tolak resep"| V
    A -->|"kelola stok, ED, detail"| S
    A --> PG
    PG -->|"stok bertambah"| S
    S -->|"detail obat tampil"| K
    T --> D
    PG --> D
    S --> D
    AD -->|"memantau"| D
    AD --> U
</pre>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Relasi Antar Tabel (ERD)</h6>
        <div class="overflow-auto">
<pre class="mermaid">
erDiagram
    roles ||--o{ users : memiliki
    users ||--o{ activity_logs : mencatat
    kategori_obat ||--o{ obat : mengelompokkan
    supplier ||--o{ obat : memasok
    pelanggan ||--o{ resep : mengajukan
    users |o--o{ resep : memverifikasi
    resep ||--|{ detail_resep : memuat
    obat ||--o{ detail_resep : tercantum
    users ||--o{ penjualan : memproses
    pelanggan |o--o{ penjualan : berbelanja
    resep |o--o{ penjualan : mendasari
    penjualan ||--|{ detail_penjualan : memuat
    obat ||--o{ detail_penjualan : terjual
    supplier ||--o{ pembelian : menerbitkan
    users ||--o{ pembelian : mencatat
    pembelian ||--|{ detail_pembelian : memuat
    obat ||--o{ detail_pembelian : dipasok
</pre>
        </div>
    </div>
</div>
@endverbatim
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.1/dist/mermaid.min.js"></script>
<script>mermaid.initialize({ startOnLoad: true, theme: 'neutral' });</script>
@endpush