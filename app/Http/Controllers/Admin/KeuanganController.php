<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    private const SALDO_AWAL_DEFAULT = 2000000;

    public function index(Request $request): View
    {
        $request->validate([
            'tahun' => 'nullable|integer|min:2000|max:'.(now()->year + 5),
            'bulan' => 'nullable|date_format:Y-m',
        ]);

        $tahun = (int) $request->input('tahun', now()->year);
        $periodeInput = $request->input(
            'bulan',
            sprintf('%04d-%02d', $tahun, now()->month)
        );
        $periode = Carbon::createFromFormat('!Y-m', $periodeInput)->startOfMonth();
        $periodeDate = $periode->toDateString();

        $pengaturanBulan = DB::table('keuangan_bulanan')
            ->whereDate('periode', $periodeDate)
            ->first();
        $pengaturanSebelumnya = $pengaturanBulan
            ? null
            : DB::table('keuangan_bulanan')
                ->whereDate('periode', '<=', $periodeDate)
                ->orderByDesc('periode')
                ->first();
        $pengaturanDefault = $pengaturanBulan ?? $pengaturanSebelumnya;

        $jumlahApotekerAktif = User::whereHas(
            'role',
            fn ($query) => $query->whereRaw('LOWER(nama_role) = ?', ['apoteker'])
        )->where('status', 'aktif')->count();

        $formKeuangan = [
            'bulan' => $periode->format('Y-m'),
            'gaji_per_apoteker' => $pengaturanDefault->gaji_per_apoteker ?? 0,
            'jumlah_apoteker' => $pengaturanBulan->jumlah_apoteker
                ?? $jumlahApotekerAktif,
            'pajak_persen' => $pengaturanDefault->pajak_persen ?? 0,
            'biaya_operasional' => $pengaturanBulan->biaya_operasional ?? 0,
        ];

        $omzetBulanan = Penjualan::query()
            ->where('status_pembayaran', 'lunas')
            ->whereYear('tanggal', $tahun)
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total_tagihan) as omzet')
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('omzet', 'bulan');

        $keuanganBulanan = DB::table('keuangan_bulanan')
            ->whereYear('periode', $tahun)
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->periode)->month);

        $riwayatBulanan = collect(range(1, 12))->map(function (int $bulan) use ($tahun, $omzetBulanan, $keuanganBulanan) {
            $pengaturan = $keuanganBulanan->get($bulan);
            $omzet = (float) ($omzetBulanan[$bulan] ?? 0);
            $gaji = $pengaturan
                ? (float) $pengaturan->gaji_per_apoteker * (int) $pengaturan->jumlah_apoteker
                : 0;
            $pajak = $pengaturan
                ? $omzet * (float) $pengaturan->pajak_persen / 100
                : 0;
            $operasional = (float) ($pengaturan->biaya_operasional ?? 0);

            return [
                'bulan' => $bulan,
                'periode' => Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y'),
                'omzet' => $omzet,
                'gaji' => $gaji,
                'pajak' => $pajak,
                'operasional' => $operasional,
                'saldo' => $omzet - $gaji - $pajak - $operasional,
                'tercatat' => (bool) $pengaturan,
            ];
        });

        $omzetTahunan = (float) $omzetBulanan->sum();
        $gajiTahunan = (float) $riwayatBulanan->sum('gaji');
        $pajakTahunan = (float) $riwayatBulanan->sum('pajak');
        $operasionalTahunan = (float) $riwayatBulanan->sum('operasional');
        $saldoTahunan = $omzetTahunan - $gajiTahunan - $pajakTahunan - $operasionalTahunan;

        $omzetSepanjangWaktu = (float) Penjualan::where('status_pembayaran', 'lunas')
            ->sum('total_tagihan');
        $pengaturanKeuangan = DB::table('pengaturan_keuangan')->where('id', 1)->first();
        $saldoAwal = (float) ($pengaturanKeuangan->saldo_awal ?? self::SALDO_AWAL_DEFAULT);
        $omzetBulananSepanjangWaktu = Penjualan::query()
            ->where('status_pembayaran', 'lunas')
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode, SUM(total_tagihan) as omzet")
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->pluck('omzet', 'periode');
        $bebanTercatat = DB::table('keuangan_bulanan')->get()->sum(function ($pengaturan) use ($omzetBulananSepanjangWaktu) {
            $periodeKey = Carbon::parse($pengaturan->periode)->format('Y-m');
            $omzetBulan = (float) ($omzetBulananSepanjangWaktu[$periodeKey] ?? 0);
            $gaji = (float) $pengaturan->gaji_per_apoteker * (int) $pengaturan->jumlah_apoteker;
            $pajak = $omzetBulan * (float) $pengaturan->pajak_persen / 100;

            return $gaji + $pajak + (float) $pengaturan->biaya_operasional;
        });
        $saldoSepanjangWaktu = $saldoAwal + $omzetSepanjangWaktu - $bebanTercatat;

        return view('admin.keuangan', compact(
            'tahun',
            'formKeuangan',
            'riwayatBulanan',
            'omzetTahunan',
            'gajiTahunan',
            'pajakTahunan',
            'operasionalTahunan',
            'saldoTahunan',
            'saldoAwal',
            'omzetSepanjangWaktu',
            'saldoSepanjangWaktu',
            'jumlahApotekerAktif'
        ));
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'gaji_per_apoteker' => 'required|numeric|min:0|max:999999999999.99',
            'jumlah_apoteker' => 'required|integer|min:0|max:1000',
            'pajak_persen' => 'required|numeric|min:0|max:100',
            'biaya_operasional' => 'required|numeric|min:0|max:999999999999.99',
            'saldo_awal' => 'required|numeric|min:0|max:999999999999.99',
        ]);

        $periode = Carbon::createFromFormat('!Y-m', $data['bulan'])->startOfMonth();
        if ($periode->greaterThan(now()->startOfMonth())) {
            return back()->withErrors([
                'bulan' => 'Data keuangan hanya dapat disimpan untuk bulan berjalan atau bulan sebelumnya.',
            ])->withInput();
        }

        DB::transaction(function () use ($data, $periode) {
            $dataSaldoAwal = [
                'saldo_awal' => $data['saldo_awal'],
                'id_user' => auth()->id(),
                'updated_at' => now(),
            ];
            $pengaturanKeuangan = DB::table('pengaturan_keuangan')
                ->where('id', 1)
                ->exists();

            if ($pengaturanKeuangan) {
                DB::table('pengaturan_keuangan')
                    ->where('id', 1)
                    ->update($dataSaldoAwal);
            } else {
                DB::table('pengaturan_keuangan')->insert($dataSaldoAwal + [
                    'id' => 1,
                    'created_at' => now(),
                ]);
            }

            $nilaiKeuangan = [
                'gaji_per_apoteker' => $data['gaji_per_apoteker'],
                'jumlah_apoteker' => $data['jumlah_apoteker'],
                'pajak_persen' => $data['pajak_persen'],
                'biaya_operasional' => $data['biaya_operasional'],
                'id_user' => auth()->id(),
                'updated_at' => now(),
            ];
            $adaPeriode = DB::table('keuangan_bulanan')
                ->whereDate('periode', $periode->toDateString())
                ->exists();

            if ($adaPeriode) {
                DB::table('keuangan_bulanan')
                    ->whereDate('periode', $periode->toDateString())
                    ->update($nilaiKeuangan);
            } else {
                DB::table('keuangan_bulanan')->insert($nilaiKeuangan + [
                    'periode' => $periode->toDateString(),
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('admin.keuangan', [
                'tahun' => $periode->year,
                'bulan' => $periode->format('Y-m'),
            ])
            ->with('success', 'Data keuangan bulan '.$periode->locale('id')->translatedFormat('F Y').' berhasil disimpan.');
    }
}
