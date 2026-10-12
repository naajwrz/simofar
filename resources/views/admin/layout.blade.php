<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | SIMOFAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --pink:#ff85a1; --pink-dark:#f76c8c; }
        body { background:#fff8fa; }
        .bg-pink { background-color:var(--pink); }
        .text-pink { color:var(--pink); }
        .btn-pink { background-color:var(--pink); color:#fff; border:0; }
        .btn-pink:hover { background-color:var(--pink-dark); color:#fff; }
        .btn-outline-pink { border:1px solid var(--pink); color:var(--pink); }
        .btn-outline-pink:hover { background-color:var(--pink); color:#fff; }
        .navbar .nav-link.active { background:rgba(255,255,255,.25); border-radius:999px; }
        .stat-icon { width:46px; height:46px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:22px; }
    </style>
</head>
<body>
@php $rute = request()->route()->getName(); @endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-pink shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-capsule-pill me-2"></i>SIMOFAR - Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navAdmin">
            <ul class="navbar-nav me-auto gap-lg-1">
                <li class="nav-item"><a class="nav-link {{ $rute === 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ str_starts_with($rute, 'admin.apoteker') ? 'active' : '' }}" href="{{ route('admin.apoteker.index') }}">Apoteker</a></li>
                <li class="nav-item"><a class="nav-link {{ str_starts_with($rute, 'admin.pelanggan') ? 'active' : '' }}" href="{{ route('admin.pelanggan.index') }}">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link {{ $rute === 'admin.diagram' ? 'active' : '' }}" href="{{ route('admin.diagram') }}">Diagram Sistem</a></li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->nama_staf }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">{{ $errors->first() }}</div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>