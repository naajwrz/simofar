<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function detailLunas()
    {
        return DB::table('detail_penjualan as d')
            ->join('penjualan as p', 'p.id_penjualan', '=', 'd.id_penjualan')
            ->join('obat as o', 'o.id_obat', '=', 'd.id_obat')
            ->where('p.status_pembayaran', 'lunas');
    }

    public function index()
    {
        $tahun = now()->year;
        $bulan = now()->month;

        $lunas = fn () => Penjualan::where('status_pembayaran', 'lunas');
        $bulanIni = fn ($q) => $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);

        $omzetHariIni = $lunas()->whereDate('tanggal', today())->sum('total_tagihan');
        $omzetBulanIni = $bulanIni($lunas())->sum('total_tagihan');
        $trxBulanIni = $bulanIni($lunas())->count();

        $labaBulanIni = $this->detailLunas()
            ->whereYear('p.tanggal', $tahun)->whereMonth('p.tanggal', $bulan)
            ->sum(DB::raw('d.jumlah * (d.harga_satuan - o.harga_beli)'));

        $modalPengadaan = Pembelian::whereYear('tanggal_pengadaan', $tahun)
            ->whereMonth('tanggal_pengadaan', $bulan)->sum('total_modal');

        // Omzet 14 hari terakhir
        $mulai = today()->subDays(13);
        $harian = Penjualan::where('status_pembayaran', 'lunas')
            ->whereDate('tanggal', '>=', $mulai)
            ->selectRaw('DATE(tanggal) as tgl, SUM(total_tagihan) as total')
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $labelHarian = [];
        $dataHarian = [];
        for ($i = 0; $i < 14; $i++) {
            $hari = $mulai->copy()->addDays($i);
            $labelHarian[] = $hari->format('d M');
            $dataHarian[] = (float) ($harian[$hari->toDateString()] ?? 0);
        }

        // 5 obat terlaris
        $terlaris = $this->detailLunas()
            ->selectRaw('o.nama_obat, SUM(d.jumlah) as terjual')
            ->groupBy('o.id_obat', 'o.nama_obat')
            ->orderByDesc('terjual')
            ->limit(5)
            ->get();
        $labelTerlaris = $terlaris->pluck('nama_obat');
        $dataTerlaris = $terlaris->pluck('terjual')->map(fn ($v) => (int) $v);

        // Pengadaan per supplier (30 hari) dan pengadaan terbaru
        $pengadaanPerSupplier = DB::table('pembelian as b')
            ->join('supplier as s', 's.id_supplier', '=', 'b.id_supplier')
            ->where('b.tanggal_pengadaan', '>=', today()->subDays(29))
            ->selectRaw('s.nama_supplier, SUM(b.total_modal) as total')
            ->groupBy('s.id_supplier', 's.nama_supplier')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $labelSupplier = $pengadaanPerSupplier->pluck('nama_supplier');
        $dataSupplier = $pengadaanPerSupplier->pluck('total')->map(fn ($v) => (float) $v);

        $pengadaanTerbaru = Pembelian::with('supplier')
            ->orderByDesc('tanggal_pengadaan')
            ->orderByDesc('id_pembelian')
            ->limit(6)
            ->get();

        // Manajemen bisnis
        $stokMenipis = Obat::whereColumn('stok', '<=', 'stok_minimum')->count();
        $hampirED = Obat::whereBetween('tanggal_expired', [today(), today()->addDays(30)])->count();
        $nilaiPersediaan = Obat::sum(DB::raw('stok * harga_beli'));
        $resepMenunggu = Resep::whereIn('status_validasi', ['diajukan', 'menunggu'])->count();
        $jumlahPelanggan = Pelanggan::count();
        $jumlahApoteker = User::whereHas('role', fn ($q) => $q->whereRaw('LOWER(nama_role) = ?', ['apoteker']))
            ->where('status', 'aktif')->count();

        $transaksiTerbaru = Penjualan::with('pelanggan')->orderByDesc('tanggal')->limit(8)->get();

        return view('admin.dashboard', compact(
            'omzetHariIni', 'omzetBulanIni', 'trxBulanIni', 'labaBulanIni', 'modalPengadaan',
            'labelHarian', 'dataHarian', 'labelTerlaris', 'dataTerlaris',
            'labelSupplier', 'dataSupplier', 'pengadaanTerbaru',
            'stokMenipis', 'hampirED', 'nilaiPersediaan', 'resepMenunggu',
            'jumlahPelanggan', 'jumlahApoteker', 'transaksiTerbaru'
        ));
    }

    public function diagram()
    {
        return view('admin.diagram');
    }
}