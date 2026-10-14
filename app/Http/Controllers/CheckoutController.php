<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Obat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    // Membuat pesanan dari keranjang browser.
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|min:1',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $penjualan = DB::transaction(function () use ($data) {
            // Akun kasir demo harus benar-benar ada dan aktif.
            $kasir = User::where('id_user', 3)
                ->where('id_role', 3)
                ->where('status', 'aktif')
                ->first();

            if (!$kasir) {
                throw ValidationException::withMessages([
                    'checkout' => 'Akun kasir demo tidak ditemukan atau tidak aktif.',
                ]);
            }

            // Gabungkan item dengan ID yang sama.
            $items = collect($data['items'])
                ->groupBy('id')
                ->map(fn ($rows) => [
                    'id' => (int) $rows->first()['id'],
                    'qty' => (int) $rows->sum('qty'),
                ])
                ->values();

            $obatIds = $items->pluck('id')->all();

            // Kunci data obat saat memvalidasi stok dan harga.
            $obats = Obat::whereIn('id_obat', $obatIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id_obat');

            $total = 0;
            $details = [];

            foreach ($items as $item) {
                $obat = $obats->get($item['id']);

                if (!$obat) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Ada obat yang tidak ditemukan.',
                    ]);
                }

                if (
                    $obat->stok < $item['qty'] ||
                    ($obat->tanggal_expired &&
                     $obat->tanggal_expired < today()->toDateString())
                ) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Stok tidak cukup atau ada obat kedaluwarsa: '
                            . $obat->nama_obat,
                    ]);
                }

                // Harga diambil dari database, bukan browser.
                $harga = (float) $obat->harga_jual;
                $subtotal = $harga * $item['qty'];
                $total += $subtotal;

                $details[] = [
                    'id_obat' => $obat->id_obat,
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            // Simpan data pelanggan tanpa akun login.
            $pelanggan = Pelanggan::create([
                'id_user' => null,
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_telepon' => $data['no_telepon'],
            ]);

            $penjualan = Penjualan::create([
                'no_transaksi' => 'SF' . now()->format('ymdHis')
                    . Str::upper(Str::random(3)),
                'id_user' => $kasir->id_user,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_resep' => null,
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

            return $penjualan;
        });

        // Simpan ID transaksi di session, bukan mempercayai ID dari browser.
        session(['simofar_checkout_id' => $penjualan->id_penjualan]);

        return redirect()->route('checkout.show');
    }

    // Menampilkan halaman QRIS simulasi untuk pesanan dalam session.
    public function show()
    {
        $id = session('simofar_checkout_id');

        abort_unless($id, 404);

        $penjualan = Penjualan::with(['detail.obat', 'pelanggan'])
            ->findOrFail($id);

        return view('katalog.pembayaran', compact('penjualan'));
    }

    // Menampilkan QRIS untuk resep yang disetujui, hanya kepada pemilik resep.
    public function showPrescriptionPayment($id)
    {
        $penjualan = $this->prescriptionSaleForCustomer($id);

        return view('katalog.pembayaran', compact('penjualan'));
    }

    // Konfirmasi pembayaran simulasi.
    public function confirm(Request $request)
    {
        $request->validate([
            'konfirmasi' => 'accepted',
        ]);

        $id = session('simofar_checkout_id');

        abort_unless($id, 404);

        $this->confirmSale($id);

        return redirect()
            ->route('checkout.show')
            ->with('success', 'Pembayaran simulasi berhasil dikonfirmasi.');
    }

    public function confirmPrescriptionPayment(Request $request, $id)
    {
        $request->validate([
            'konfirmasi' => 'accepted',
        ]);

        $penjualan = $this->prescriptionSaleForCustomer($id);
        $this->confirmSale($penjualan->id_penjualan);

        return redirect()
            ->route('pelanggan.permintaan.pembayaran', $id)
            ->with('success', 'Pembayaran simulasi berhasil dikonfirmasi.');
    }

    private function prescriptionSaleForCustomer($id): Penjualan
    {
        abort_unless(auth()->user()->isPelanggan(), 403);

        $pelanggan = Pelanggan::where('id_user', auth()->id())->firstOrFail();

        return Penjualan::with(['detail.obat', 'pelanggan'])
            ->where('id_resep', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();
    }

    private function confirmSale($id): void
    {
        DB::transaction(function () use ($id) {
            $penjualan = Penjualan::where('id_penjualan', $id)
                ->lockForUpdate()
                ->firstOrFail();

            // Klik ulang tidak boleh mengurangi stok lagi.
            if ($penjualan->status_pembayaran === 'lunas') {
                return;
            }

            if (
                $penjualan->status_pembayaran !== 'pending' ||
                $penjualan->status_pesanan !== 'menunggu_pembayaran'
            ) {
                throw ValidationException::withMessages([
                    'konfirmasi' => 'Pesanan ini tidak bisa dikonfirmasi.',
                ]);
            }

            $details = DetailPenjualan::where(
                'id_penjualan',
                $penjualan->id_penjualan
            )->get();

            if ($details->isEmpty()) {
                throw ValidationException::withMessages([
                    'konfirmasi' => 'Detail pesanan tidak ditemukan.',
                ]);
            }

            // Kunci semua obat berdasarkan ID secara konsisten.
            $ids = $details->pluck('id_obat')->unique()->sort()->values();

            $obats = Obat::whereIn('id_obat', $ids)
                ->orderBy('id_obat')
                ->lockForUpdate()
                ->get()
                ->keyBy('id_obat');

            // Periksa seluruh stok sebelum mengurangi satu pun.
            foreach ($details as $detail) {
                $obat = $obats->get($detail->id_obat);

                if (
                    !$obat ||
                    $obat->stok < $detail->jumlah ||
                    ($obat->tanggal_expired &&
                     $obat->tanggal_expired < today()->toDateString())
                ) {
                    throw ValidationException::withMessages([
                        'konfirmasi' =>
                            'Stok berubah atau obat kedaluwarsa. '
                            . 'Pesanan belum dibayar; silakan hubungi apotek.',
                    ]);
                }
            }

            foreach ($details as $detail) {
                $obats->get($detail->id_obat)
                    ->decrement('stok', $detail->jumlah);
            }

            $penjualan->update([
                'status_pembayaran' => 'lunas',
                'status_pesanan' => 'menunggu_diproses',
                'bayar' => $penjualan->total_tagihan,
                'kembalian' => 0,
                'waktu_pembayaran' => now(),
            ]);
        });
    }
}
