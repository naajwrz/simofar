<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\DetailPenjualan;
use App\Models\DetailResep;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ResepController extends Controller
{
    public function index()
    {
        $resepList = Resep::with(['pelanggan', 'detailResep.obat'])
            ->where('status_validasi', '!=', 'diajukan')
            ->orderBy('tanggal_terbit', 'desc')
            ->get();

        $permintaan = Resep::with(['pelanggan', 'detailResep.obat'])
            ->where('status_validasi', 'diajukan')
            ->orderBy('id_resep')
            ->get();

        $pelanggans = Pelanggan::whereHas('user', function ($query) {
            $query->where('status', 'aktif')
                ->whereHas('role', fn ($roleQuery) => $roleQuery->whereRaw('LOWER(nama_role) = ?', ['pelanggan']));
        })->orderBy('nama_pelanggan')->get();
        $obats = Obat::orderBy('nama_obat')->get();
        $noResepBaru = $this->nomorResepBaru();

        return view('apoteker.resep.index', compact(
            'resepList', 'permintaan', 'pelanggans', 'obats', 'noResepBaru'
        ));
    }

    public function patients(): JsonResponse
    {
        abort_unless(auth()->user()->isApoteker(), 403);

        $pelanggans = Pelanggan::whereHas('user', function ($query) {
            $query->where('status', 'aktif')
                ->whereHas('role', fn ($roleQuery) => $roleQuery->whereRaw('LOWER(nama_role) = ?', ['pelanggan']));
        })
            ->orderBy('nama_pelanggan')
            ->get(['id_pelanggan', 'nama_pelanggan']);

        return response()->json($pelanggans);
    }

    // Nomor resep berikutnya yang belum dipakai.
    private function nomorResepBaru(): string
    {
        $n = (int) Resep::max('id_resep') + 1;

        do {
            $kode = 'RSP-'.str_pad($n++, 4, '0', STR_PAD_LEFT);
        } while (Resep::where('no_resep', $kode)->exists());

        return $kode;
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_resep' => 'required|max:20|unique:resep,no_resep',
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'nama_dokter' => 'required|max:100',
            'tanggal_terbit' => 'required|date',
            'obat' => 'required|array|min:1',
            'obat.*.id_obat' => 'required|exists:obat,id_obat',
            'obat.*.jumlah' => 'required|integer|min:1',
            'obat.*.aturan_pakai' => 'nullable|max:100',
        ], [
            'no_resep.unique' => 'No. resep sudah dipakai, gunakan nomor lain.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $resep = Resep::create([
                    'no_resep' => $request->no_resep,
                    'id_pelanggan' => $request->id_pelanggan,
                    'id_user' => auth()->user()->id_user,
                    'nama_dokter' => $request->nama_dokter,
                    'tanggal_terbit' => $request->tanggal_terbit,
                    'status_validasi' => 'menunggu',
                ]);

                foreach ($request->obat as $item) {
                    DetailResep::create([
                        'id_resep' => $resep->id_resep,
                        'id_obat' => $item['id_obat'],
                        'jumlah' => $item['jumlah'],
                        'aturan_pakai' => $item['aturan_pakai'] ?? null,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Resep gagal disimpan: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Resep berhasil ditambahkan dan menunggu verifikasi!');
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate(['status_validasi' => 'required|in:valid,tidak_valid']);

        DB::transaction(function () use ($request, $id) {
            $resep = Resep::where('status_validasi', 'menunggu')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($request->status_validasi === 'valid') {
                $this->buatPesananQris($resep);
            }

            $resep->update([
                'status_validasi' => $request->status_validasi,
                'id_user' => auth()->user()->id_user,
            ]);
        });

        $pesan = $request->status_validasi === 'valid'
            ? 'Resep disetujui. Pelanggan dapat membayar obat melalui QRIS di halaman Permintaan Saya.'
            : 'Resep ditandai tidak valid.';

        return redirect()->back()->with('success', $pesan);
    }

    // ACC permintaan pelanggan: setujui resep dan siapkan pesanan QRIS.
    public function acc($id)
    {
        DB::transaction(function () use ($id) {
            $resep = Resep::with('detailResep')
                ->where('status_validasi', 'diajukan')
                ->lockForUpdate()
                ->findOrFail($id);

            $this->buatPesananQris($resep);

            $resep->update([
                'status_validasi' => 'valid',
                'id_user' => auth()->user()->id_user,
            ]);
        });

        return back()->with('success', 'Resep disetujui. QRIS tersedia di halaman Permintaan Saya milik pelanggan.');
    }

    private function buatPesananQris(Resep $resep): void
    {
        if (Penjualan::where('id_resep', $resep->id_resep)->exists()) {
            return;
        }

        $resep->loadMissing('detailResep');
        if ($resep->detailResep->isEmpty()) {
            throw ValidationException::withMessages([
                'resep' => 'Resep tidak memiliki detail obat.',
            ]);
        }

        $obatIds = $resep->detailResep->pluck('id_obat')->unique()->sort()->values();
        $obats = Obat::whereIn('id_obat', $obatIds)
            ->orderBy('id_obat')
            ->lockForUpdate()
            ->get()
            ->keyBy('id_obat');

        $total = 0;
        $details = [];

        foreach ($resep->detailResep as $detail) {
            $obat = $obats->get($detail->id_obat);

            if (
                ! $obat ||
                $obat->stok < $detail->jumlah ||
                ($obat->tanggal_expired &&
                 $obat->tanggal_expired < today()->toDateString())
            ) {
                throw ValidationException::withMessages([
                    'resep' => 'Stok tidak cukup atau obat kedaluwarsa: '
                        .($obat->nama_obat ?? 'obat tidak ditemukan').'.',
                ]);
            }

            $harga = (float) $obat->harga_jual;
            $subtotal = $harga * $detail->jumlah;
            $total += $subtotal;
            $details[] = [
                'id_obat' => $obat->id_obat,
                'jumlah' => $detail->jumlah,
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        $penjualan = Penjualan::create([
            'no_transaksi' => 'SF'.now()->format('ymdHis')
                .Str::upper(Str::random(3)),
            'id_user' => auth()->user()->id_user,
            'id_pelanggan' => $resep->id_pelanggan,
            'id_resep' => $resep->id_resep,
            'tanggal' => now(),
            'total_tagihan' => $total,
            'bayar' => 0,
            'kembalian' => 0,
            'metode_bayar' => 'qris',
            'status_pembayaran' => 'pending',
            'status_pesanan' => 'menunggu_pembayaran',
        ]);

        foreach ($details as $detail) {
            $detail['id_penjualan'] = $penjualan->id_penjualan;
            DetailPenjualan::create($detail);
        }
    }

    public function tolakPermintaan($id)
    {
        $resep = Resep::where('status_validasi', 'diajukan')->findOrFail($id);
        $resep->update([
            'status_validasi' => 'tidak_valid',
            'id_user' => auth()->user()->id_user,
        ]);

        return back()->with('success', 'Permintaan ditolak.');
    }
}
