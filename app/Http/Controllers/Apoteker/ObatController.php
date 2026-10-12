<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\KategoriObat;

class ObatController extends Controller
{
    public function index()
    {
        // Mengambil seluruh data obat
        $obats = Obat::all();

        // Cari obat yang akan kadaluarsa dalam 30 hari ke depan atau sudah kadaluarsa
        $hariIni = Carbon::now();
        $batasKadaluarsa = Carbon::now()->addDays(30);

        $obatMendekatiExpired = Obat::where('tanggal_expired', '<=', $batasKadaluarsa)->get();

        return view('apoteker.obat.index', compact('obats', 'obatMendekatiExpired'));
    }
    public function storeObat(Request $request)
    {
        $request->validate([
            'kode_obat' => 'required',
            'nama_obat' => 'required',
            'id_kategori' => 'required',
            'satuan' => 'required',
            'stok' => 'required|numeric',
            'tanggal_expired' => 'required|date',
        ]);

        Obat::create([
            'kode_obat' => $request->kode_obat,
            'nama_obat' => $request->nama_obat,
            'id_kategori' => $request->id_kategori,
            'satuan' => $request->satuan,
            'stok' => $request->stok,
            'tanggal_expired' => $request->tanggal_expired,
        ]);

        return redirect()->back()->with(
            'success',
            'Obat baru berhasil ditambahkan!'
        );
    }
    public function storeKategori(Request $request)
{
            $request->validate([
            'kode_kategori' => 'required|max:10|unique:kategori_obat,kode_kategori',
            'nama_kategori' => 'required|max:50',
        ], [
            'kode_kategori.unique' => 'Kode kategori sudah dipakai, gunakan kode lain (mis. KAT-11).',
        ]);

    KategoriObat::create([
        'kode_kategori' => $request->kode_kategori,
        'nama_kategori' => $request->nama_kategori,
        'keterangan' => $request->keterangan ?? null,
    ]);

    return redirect()->back()->with(
        'success',
        'Kategori baru berhasil ditambahkan!'
    );
}
    public function update(Request $request, $id)
    {
        $request->validate([
            'stok' => 'required|numeric|min:0',
            'tanggal_expired' => 'required|date',
        ]);

        $obat = Obat::findOrFail($id);
        $obat->update([
            'stok' => $request->stok,
            'tanggal_expired' => $request->tanggal_expired,
        ]);

        return redirect()->back()->with('success', 'Stok dan tanggal kadaluarsa ' . $obat->nama_obat . ' berhasil diperbarui!');
    }
}