@extends('admin.layout')
@section('title', 'Data Pelanggan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h3 class="fw-bold mb-0">Data Pelanggan</h3>
        <p class="text-muted small mb-0">Info pelanggan, riwayat belanja, dan status akun.</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / telepon / email">
        <button class="btn btn-pink"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pelanggan</th><th>Kontak</th><th class="text-end">Transaksi</th>
                    <th class="text-end">Total Belanja</th><th>Akun</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggans as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->nama_pelanggan }}</div>
                            <div class="small text-muted">{{ $p->alamat }}</div>
                        </td>
                        <td>
                            {{ $p->no_telepon ?? '-' }}
                            <div class="small text-muted">{{ $p->email }}</div>
                        </td>
                        <td class="text-end">{{ $p->jumlah_transaksi }}</td>
                        <td class="text-end">Rp{{ number_format($p->total_belanja, 0, ',', '.') }}</td>
                        <td>
                            @if($p->id_user)
                                <span class="badge text-bg-{{ $p->status_akun === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($p->status_akun) }}</span>
                            @else
                                <span class="badge text-bg-light border">Tanpa akun</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-pink rounded-pill px-3"
                                    data-bs-toggle="modal" data-bs-target="#edit{{ $p->id_pelanggan }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Pelanggan tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pelanggans as $p)
<div class="modal fade" id="edit{{ $p->id_pelanggan }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0" method="POST" action="{{ route('admin.pelanggan.update', $p->id_pelanggan) }}">
            @csrf
            @method('PUT')
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit {{ $p->nama_pelanggan }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama</label><input name="nama_pelanggan" class="form-control" value="{{ $p->nama_pelanggan }}" required></div>
                <div class="mb-3"><label class="form-label">No. telepon</label><input name="no_telepon" class="form-control" maxlength="15" value="{{ $p->no_telepon }}"></div>
                <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2">{{ $p->alamat }}</textarea></div>
                @if($p->id_user)
                    <div class="mb-3">
                        <label class="form-label">Status akun</label>
                        <select name="status_akun" class="form-select">
                            <option value="aktif" {{ $p->status_akun === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $p->status_akun === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-pink">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection