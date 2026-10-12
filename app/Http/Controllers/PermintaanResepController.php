<?php

namespace App\Http\Controllers;

use App\Models\DetailResep;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermintaanResepController extends Controller
{
        private function pelangganLogin(): Pelanggan
    {
        $user = auth()->user();
        abort_unless($user && $user->isPelanggan(), 403, 'Khusus akun pelanggan.');

        return Pelanggan::firstOrCreate(
            ['id_user' => $user->id_user],
            ['nama_pelanggan' => $user->nama_staf]
        );
    }

    public function index()
    {
        $pelanggan = $this->pelangganLogin();

        $permintaan = Resep::with('detailResep.obat')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderByDesc('id_resep')
            ->get();

        return view('pelanggan.permintaan', compact('permintaan'));
    }

    public function store(Request $request)
    {
        $pelanggan = $this->pelangganLogin();

        $data = $request->validate([
            'id_obat' => 'required|exists:obat,id_obat',
            'jumlah' => 'required|integer|min:1|max:100',
            'nama_dokter' => 'required|string|max:100',
            'tanggal_terbit' => 'required|date|before_or_equal:today',
            'aturan_pakai' => 'nullable|string|max:100',
            'catatan_pelanggan' => 'nullable|string|max:500',
            'foto_resep' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $obat = Obat::findOrFail($data['id_obat']);

        if ($obat->golongan !== 'keras') {
            return back()->withErrors(['id_obat' => 'Obat ini tidak memerlukan resep.']);
        }

        $foto = null;
        if ($request->hasFile('foto_resep')) {
            $file = $request->file('foto_resep');
            $nama = Str::random(24) . '.' . $file->extension();
            $file->move(public_path('uploads/resep'), $nama);
            $foto = 'uploads/resep/' . $nama;
        }

               try {
            DB::transaction(function () use ($data, $pelanggan, $foto) {
                $resep = Resep::create([
                    'no_resep' => 'REQ' . now()->format('ymdHis') . Str::upper(Str::random(3)),
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'id_user' => null,
                    'nama_dokter' => $data['nama_dokter'],
                    'tanggal_terbit' => $data['tanggal_terbit'],
                    'status_validasi' => 'diajukan',
                    'foto_resep' => $foto,
                    'catatan_pelanggan' => $data['catatan_pelanggan'] ?? null,
                ]);

                DetailResep::create([
                    'id_resep' => $resep->id_resep,
                    'id_obat' => $data['id_obat'],
                    'jumlah' => $data['jumlah'],
                    'aturan_pakai' => $data['aturan_pakai'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Permintaan gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->route('pelanggan.permintaan.index')
            ->with('success', 'Permintaan terkirim. Apoteker akan memeriksanya.');
    }
}