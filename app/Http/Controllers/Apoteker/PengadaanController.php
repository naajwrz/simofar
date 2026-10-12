<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\DetailPembelian;
use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('status_kerjasama', 'aktif')->orderBy('nama_supplier')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        $pembelianList = Pembelian::with(['supplier', 'user', 'details.obat'])
            ->orderByDesc('tanggal_pengadaan')
            ->orderByDesc('id_pembelian')
            ->get();

        // Saran nomor faktur dan kode supplier yang belum dipakai
        $n = Pembelian::count() + 1;
        do {
            $noFakturBaru = 'FKT-' . now()->format('ymd') . '-' . str_pad($n++, 3, '0', STR_PAD_LEFT);
        } while (Pembelian::where('no_faktur', $noFakturBaru)->exists());

        $maks = Supplier::pluck('kode_supplier')
            ->map(fn ($k) => (int) preg_replace('/\D/', '', $k))
            ->max() ?? 0;
        $kodeSupplierBaru = 'SUP-' . str_pad($maks + 1, 2, '0', STR_PAD_LEFT);

        return view('apoteker.pengadaan.index', compact(
            'suppliers', 'obats', 'pembelianList', 'noFakturBaru', 'kodeSupplierBaru'
        ));
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'kode_supplier' => 'required|string|max:10|unique:supplier,kode_supplier',
            'nama_supplier' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'status_kerjasama' => 'required|in:aktif,nonaktif',
        ], [
            'kode_supplier.unique' => 'Kode supplier sudah dipakai, gunakan kode lain.',
        ]);

        Supplier::create($request->only(
            'kode_supplier', 'nama_supplier', 'no_telepon', 'alamat', 'status_kerjasama'
        ));

        return redirect()->route('apoteker.pengadaan.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|max:20|unique:pembelian,no_faktur',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'tanggal_pengadaan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_obat' => 'required|exists:obat,id_obat',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ], [
            'no_faktur.unique' => 'Nomor faktur sudah dipakai, gunakan nomor lain.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $total = 0;
                foreach ($request->items as $item) {
                    $total += $item['jumlah'] * $item['harga_beli'];
                }

                $pembelian = Pembelian::create([
                    'no_faktur' => $request->no_faktur,
                    'id_supplier' => $request->id_supplier,
                    'id_user' => auth()->id(),
                    'tanggal_pengadaan' => $request->tanggal_pengadaan,
                    'total_modal' => $total,
                ]);

                foreach ($request->items as $item) {
                    DetailPembelian::create([
                        'id_pembelian' => $pembelian->id_pembelian,
                        'id_obat' => $item['id_obat'],
                        'jumlah' => $item['jumlah'],
                        'harga_beli' => $item['harga_beli'],
                        'subtotal' => $item['jumlah'] * $item['harga_beli'],
                    ]);

                    // Stok bertambah dan harga beli ikut diperbarui
                    $obat = Obat::findOrFail($item['id_obat']);
                    $obat->increment('stok', $item['jumlah']);
                    $obat->update(['harga_beli' => $item['harga_beli']]);
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Pengadaan gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pengadaan berhasil disimpan dan stok obat bertambah!');
    }
}