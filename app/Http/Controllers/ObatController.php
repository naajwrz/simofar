<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\KategoriObat;
use App\Models\Obat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::with('kategori')->orderBy('nama_obat')->get();
        $obatMendekatiExpired = Obat::where('tanggal_expired', '<=', Carbon::now()->addDays(30))->get();

        return view('apoteker.obat.index', compact('obats', 'obatMendekatiExpired'));
    }

    // Aturan yang sama untuk tambah dan ubah detail obat.
    private function detailRules(): array
    {
        return [
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'golongan' => 'required|in:bebas,bebas_terbatas,keras',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'tanggal_expired' => 'required|date',
            'deskripsi' => 'nullable|string|max:2000',
            'indikasi' => 'nullable|string|max:2000',
            'aturan_pakai' => 'nullable|string|max:2000',
            'efek_samping' => 'nullable|string|max:2000',
            'kontraindikasi' => 'nullable|string|max:2000',
            'catatan_apoteker' => 'nullable|string|max:2000',
        ];
    }

    public function storeObat(Request $request)
    {
        $data = $request->validate($this->detailRules() + [
            'kode_obat' => 'required|max:15|unique:obat,kode_obat',
            'nama_obat' => 'required|max:100',
            'id_kategori' => 'required|exists:kategori_obat,id_kategori',
            'satuan' => 'required|max:20',
        ]);

        Obat::create($data);

        return redirect()->back()->with('success', 'Obat baru berhasil ditambahkan!');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required',
            'nama_kategori' => 'required',
        ]);

        KategoriObat::create([
            'kode_kategori' => $request->kode_kategori,
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan ?? null,
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->detailRules());

        $obat = Obat::findOrFail($id);
        $obat->update($data);

        return redirect()->back()->with('success', 'Detail ' . $obat->nama_obat . ' berhasil diperbarui!');
    }
}