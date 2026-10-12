
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pelanggan - SIMOFAR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: linear-gradient(135deg, #f5c2bd, #f8d9d6);
            font-family: Arial, sans-serif;
        }

        .register-card {
            width: 100%;
            max-width: 430px;
            background: #faecdf;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(120, 60, 60, .12);
        }

        .brand {
            color: #ec4899;
            font-weight: 800;
            font-size: 25px;
        }

        .form-label {
            font-size: 13px;
            margin-bottom: 5px;
            color: #514044;
        }

        .form-control {
            border: 1px solid #ddceca;
            border-radius: 8px;
            background: #fff8f1;
            font-size: 14px;
            padding: 9px 12px;
        }

        .form-control:focus {
            border-color: #f472a8;
            box-shadow: 0 0 0 .2rem rgba(236, 72, 153, .12);
        }

        .btn-pink {
            background: #f47291;
            border: none;
            color: white;
        }

        .btn-pink:hover {
            background: #ec4899;
            color: white;
        }

        .login-link {
            color: #ec4899;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @media (max-height: 750px) {
            body {
                align-items: flex-start;
            }

            .register-card {
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>

<div class="card register-card">
    <div class="card-body p-4">

        <div class="text-center mb-4">
            <h2 class="brand mb-2">SIMOFAR 🩷</h2>
            <h5 class="fw-bold">Buat Akun Pelanggan</h5>
            <p class="text-muted small mb-0">
                Daftar untuk menggunakan layanan SIMOFAR.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_pelanggan"
                       class="form-control"
                       value="{{ old('nama_pelanggan') }}"
                       maxlength="100" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       maxlength="100" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="no_telepon"
                       class="form-control"
                       value="{{ old('no_telepon') }}"
                       maxlength="15">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control"
                          rows="2">{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password"
                       class="form-control"
                       minlength="8" required>
                <small class="text-muted">Minimal 8 karakter.</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password"
                       name="password_confirmation"
                       class="form-control"
                       minlength="8" required>
            </div>

            <button type="submit"
                    class="btn btn-pink w-100 rounded-pill py-2 fw-semibold">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center mt-4 mb-0 small">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="login-link fw-semibold">
                Login di sini
            </a>
        </p>

    </div>
</div>

</body>
</html>
```