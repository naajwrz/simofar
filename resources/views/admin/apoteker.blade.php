@extends('admin.layout')
@section('title', 'Data Apoteker')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="fw-bold mb-0">Data Apoteker</h3>
        <p class="text-muted small mb-0">Kelola akun apoteker apotek.</p>
    </div>
    <button class="btn btn-pink rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-person-plus me-1"></i> Tambah Apoteker
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama</th><th>Email</th><th>Status</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($apotekers as $a)
                    <tr>
                        <td class="fw-semibold">{{ $a->nama_staf }}</td>
                        <td>{{ $a->email }}</td>
                        <td>
                            <span class="badge text-bg-{{ $a->status === 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-pink rounded-pill px-3"
                                    data-bs-toggle="modal" data-bs-target="#edit{{ $a->id_user }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada apoteker.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0" method="POST" action="{{ route('admin.apoteker.store') }}">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Apoteker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama</label><input name="nama_staf" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Password (min. 8 karakter)</label><input type="password" name="password" class="form-control" minlength="8" required></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-pink">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal edit -->
@foreach($apotekers as $a)
<div class="modal fade" id="edit{{ $a->id_user }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0" method="POST" action="{{ route('admin.apoteker.update', $a->id_user) }}">
            @csrf
            @method('PUT')
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit {{ $a->nama_staf }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama</label><input name="nama_staf" class="form-control" value="{{ $a->nama_staf }}" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $a->email }}" required></div>
                <div class="mb-3">
                    <label class="form-label">Status akun</label>
                    <select name="status" class="form-select">
                        <option value="aktif" {{ $a->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $a->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password baru <span class="text-muted small">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-control" minlength="8">
                </div>
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