<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\KategoriObat;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with('kategori')
            ->where('stok', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                  ->orWhereDate('tanggal_expired', '>=', today());
            });

        if ($request->filled('search')) {
            $query->where(
                'nama_obat',
                'like',
                '%' . $request->search . '%'
            );
        }

        if ($request->filled('kategori')) {
            $query->where(
                'id_kategori',
                $request->kategori
            );
        }

        $obats = $query->orderBy('nama_obat')->get();
        $kategoris = KategoriObat::all();

        return view('katalog.index', compact('obats', 'kategoris'));
    }
}
