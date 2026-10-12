<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\DetailResep;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $obats = Obat::orderBy('nama_obat')->get();
        $noResepBaru = $this->nomorResepBaru();

        return view('apoteker.resep.index', compact(
            'resepList', 'permintaan', 'pelanggans', 'obats', 'noResepBaru'
        ));
    }

    // Nomor resep berikutnya yang belum dipakai.
    private function nomorResepBaru(): string
    {
        $n = (int) Resep::max('id_resep') + 1;

        do {
            $kode = 'RSP-' . str_pad($n++, 4, '0', STR_PAD_LEFT);
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
            return back()->withInput()->with('error', 'Resep gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Resep berhasil ditambahkan dan menunggu verifikasi!');
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate(['status_validasi' => 'required|in:valid,tidak_valid']);

        $resep = Resep::where('status_validasi', 'menunggu')->findOrFail($id);
        $resep->update(['status_validasi' => $request->status_validasi]);

        return redirect()->back()->with('success', 'Status validasi resep berhasil diperbarui!');
    }

    // ACC permintaan pelanggan: masuk ke daftar verifikasi resep.
    public function acc($id)
    {
        $resep = Resep::where('status_validasi', 'diajukan')->findOrFail($id);
        $resep->update([
            'status_validasi' => 'menunggu',
            'id_user' => auth()->user()->id_user,
        ]);

        return back()->with('success', 'Permintaan diterima dan masuk ke daftar verifikasi resep.');
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