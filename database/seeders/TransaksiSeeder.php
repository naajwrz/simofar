<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DummyTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(77);

        $obats = DB::table('obat')->get();
        if ($obats->count() < 10) { 
            $this->command->warn('Jalankan ObatSeeder dulu.');
            return;
        }
        $semua = $obats->all();
        $bebas = $obats->where('golongan', '!=', 'keras')->values()->all();

        $suppliers = DB::table('supplier')->where('status_kerjasama', 'aktif')->pluck('id_supplier')->all();
        $pelangganIds = DB::table('pelanggan')->pluck('id_pelanggan')->all();

        $idPerRole = function (string $role) {
            return DB::table('users')
                ->join('roles', 'roles.id_role', '=', 'users.id_role')
                ->whereRaw('LOWER(roles.nama_role) = ?', [$role])
                ->value('users.id_user');
        };
        $apoteker = $idPerRole('apoteker') ?? DB::table('users')->value('id_user');
        $kasir = $idPerRole('kasir') ?? $apoteker;

        // Hapus dummy lama supaya seeder aman dijalankan ulang
        $lamaBeli = DB::table('pembelian')->where('no_faktur', 'like', 'FKT-D%')->pluck('id_pembelian');
        DB::table('detail_pembelian')->whereIn('id_pembelian', $lamaBeli)->delete();
        DB::table('pembelian')->whereIn('id_pembelian', $lamaBeli)->delete();

        $lamaJual = DB::table('penjualan')->where('no_transaksi', 'like', 'TRX-D%')->pluck('id_penjualan');
        DB::table('detail_penjualan')->whereIn('id_penjualan', $lamaJual)->delete();
        DB::table('penjualan')->whereIn('id_penjualan', $lamaJual)->delete();

        // ---------- 12 faktur pengadaan dalam 30 hari terakhir ----------
        for ($i = 1; $i <= 12; $i++) {
            $id = DB::table('pembelian')->insertGetId([
                'no_faktur' => 'FKT-D' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'id_supplier' => $suppliers[array_rand($suppliers)],
                'id_user' => $apoteker,
                'tanggal_pengadaan' => now()->subDays(mt_rand(0, 29))->toDateString(),
                'total_modal' => 0,
            ]);

            $total = 0;
            foreach ((array) array_rand($semua, mt_rand(2, 4)) as $k) {
                $o = $semua[$k];
                $jumlah = mt_rand(20, 100);
                $subtotal = $jumlah * $o->harga_beli;
                $total += $subtotal;

                DB::table('detail_pembelian')->insert([
                    'id_pembelian' => $id,
                    'id_obat' => $o->id_obat,
                    'jumlah' => $jumlah,
                    'harga_beli' => $o->harga_beli,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::table('pembelian')->where('id_pembelian', $id)->update(['total_modal' => $total]);
        }

        // ---------- 70 transaksi penjualan lunas (lebih ramai di hari-hari terakhir) ----------
        $punyaStatus = Schema::hasColumn('penjualan', 'status_pembayaran');
        $punyaPesanan = Schema::hasColumn('penjualan', 'status_pesanan');
        $punyaWaktuBayar = Schema::hasColumn('penjualan', 'waktu_pembayaran');

        for ($i = 1; $i <= 70; $i++) {
            $hariLalu = (int) floor(29 * ((mt_rand(0, 100) / 100) ** 2));
            $waktu = now()->subDays($hariLalu)->setTime(mt_rand(8, 20), mt_rand(0, 59), mt_rand(0, 59));

            $baris = [];
            $total = 0;
            foreach ((array) array_rand($bebas, min(mt_rand(1, 4), count($bebas))) as $k) {
                $o = $bebas[$k];
                $qty = mt_rand(1, 4);
                $subtotal = $o->harga_jual * $qty;
                $total += $subtotal;
                $baris[] = [
                    'id_obat' => $o->id_obat,
                    'jumlah' => $qty,
                    'harga_satuan' => $o->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $metode = ['tunai', 'transfer', 'qris'][mt_rand(0, 2)];
            $bayar = $metode === 'tunai' ? ceil($total / 5000) * 5000 : $total;

            $row = [
                'no_transaksi' => 'TRX-D' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'id_user' => $kasir,
                'id_pelanggan' => (mt_rand(0, 100) < 60 && $pelangganIds) ? $pelangganIds[array_rand($pelangganIds)] : null,
                'id_resep' => null,
                'tanggal' => $waktu,
                'total_tagihan' => $total,
                'bayar' => $bayar,
                'kembalian' => $bayar - $total,
                'metode_bayar' => $metode,
            ];
            if ($punyaStatus) { $row['status_pembayaran'] = 'lunas'; }
            if ($punyaPesanan) { $row['status_pesanan'] = 'selesai'; }
            if ($punyaWaktuBayar) { $row['waktu_pembayaran'] = $waktu; }

            $idJual = DB::table('penjualan')->insertGetId($row);

            foreach ($baris as $b) {
                DB::table('detail_penjualan')->insert($b + ['id_penjualan' => $idJual]);
            }
        }

        $this->command->info('Selesai: 12 faktur pengadaan dan 70 transaksi penjualan dummy.');
    }
}