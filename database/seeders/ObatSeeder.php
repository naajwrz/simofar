<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // angka acak selalu sama, hasil seeder konsisten

        // ---------- Kategori (yang sudah ada dilewati) ----------
        $kategori = [
            'KAT-01' => ['Analgesik', 'Pereda nyeri dan demam'],
            'KAT-02' => ['Antibiotik', 'Melawan infeksi bakteri'],
            'KAT-03' => ['Vitamin', 'Suplemen dan vitamin'],
            'KAT-04' => ['Pencernaan', 'Obat lambung, mual, dan gangguan pencernaan'],
            'KAT-05' => ['Batuk dan Flu', 'Obat batuk, pilek, dan flu'],
            'KAT-06' => ['Alergi', 'Antihistamin dan obat alergi'],
            'KAT-07' => ['Kardiovaskular', 'Obat tekanan darah, kolesterol, dan jantung'],
            'KAT-08' => ['Antidiabetes', 'Obat pengendali gula darah'],
            'KAT-09' => ['Obat Kulit', 'Obat luar untuk kulit'],
            'KAT-10' => ['Obat Mata dan Telinga', 'Tetes mata dan tetes telinga'],
        ];

        foreach ($kategori as $kode => [$nama, $ket]) {
            if (!DB::table('kategori_obat')->where('kode_kategori', $kode)->exists()) {
                DB::table('kategori_obat')->insert([
                    'kode_kategori' => $kode,
                    'nama_kategori' => $nama,
                    'keterangan' => $ket,
                ]);
            }
        }
        $idKategori = DB::table('kategori_obat')->pluck('id_kategori', 'kode_kategori');

        // ---------- Supplier (yang sudah ada dilewati) ----------
        $supplier = [
            'SUP-03' => ['PT Medika Nusantara', '0341555101', 'Jl. Soekarno Hatta No. 12, Malang', 'aktif'],
            'SUP-04' => ['PT Farmasi Sejahtera Abadi', '031555102', 'Jl. Raya Darmo No. 45, Surabaya', 'aktif'],
            'SUP-05' => ['CV Sehat Bersama', '0354555103', 'Jl. Brawijaya No. 8, Kediri', 'aktif'],
            'SUP-06' => ['PT Bina Husada Farma', '021555104', 'Jl. Gatot Subroto No. 30, Jakarta', 'aktif'],
            'SUP-07' => ['PT Tirta Medika Utama', '024555105', 'Jl. Pandanaran No. 21, Semarang', 'aktif'],
            'SUP-08' => ['CV Apotek Mitra Jaya', '0341555106', 'Jl. Panglima Sudirman No. 5, Batu', 'aktif'],
            'SUP-09' => ['PT Nusantara Pharma Prima', '022555107', 'Jl. Asia Afrika No. 77, Bandung', 'aktif'],
            'SUP-10' => ['PT Karya Waluya Farma', '0274555108', 'Jl. Malioboro No. 19, Yogyakarta', 'nonaktif'],
        ];

        foreach ($supplier as $kode => [$nama, $telp, $alamat, $status]) {
            if (!DB::table('supplier')->where('kode_supplier', $kode)->exists()) {
                DB::table('supplier')->insert([
                    'kode_supplier' => $kode,
                    'nama_supplier' => $nama,
                    'no_telepon' => $telp,
                    'alamat' => $alamat,
                    'status_kerjasama' => $status,
                ]);
            }
        }
        $supplierIds = DB::table('supplier')
            ->where('status_kerjasama', 'aktif')
            ->orderBy('id_supplier')
            ->pluck('id_supplier')
            ->all();

        // ---------- Info detail per kategori ----------
        $info = [
            'KAT-01' => [
                'indikasi' => 'Meredakan nyeri ringan hingga sedang dan menurunkan demam.',
                'aturan_pakai' => 'Diminum sesudah makan sesuai anjuran dokter atau apoteker.',
                'efek_samping' => 'Mual, nyeri ulu hati, ruam kulit (jarang).',
                'kontraindikasi' => 'Alergi bahan aktif; hati-hati pada gangguan hati, ginjal, dan lambung.',
            ],
            'KAT-02' => [
                'indikasi' => 'Infeksi akibat bakteri sesuai petunjuk dokter.',
                'aturan_pakai' => 'Habiskan sesuai durasi yang diresepkan, jangan dihentikan sendiri.',
                'efek_samping' => 'Mual, diare, ruam kulit.',
                'kontraindikasi' => 'Riwayat alergi terhadap antibiotik sejenis.',
            ],
            'KAT-03' => [
                'indikasi' => 'Memenuhi kebutuhan vitamin dan mineral harian.',
                'aturan_pakai' => '1 kali sehari sesudah makan, kecuali ditentukan lain.',
                'efek_samping' => 'Gangguan lambung ringan jika dosis berlebih.',
                'kontraindikasi' => 'Alergi bahan; konsultasikan jika sedang hamil atau menyusui.',
            ],
            'KAT-04' => [
                'indikasi' => 'Meredakan keluhan lambung, mual, kembung, atau gangguan buang air besar.',
                'aturan_pakai' => 'Ikuti petunjuk pada kemasan atau resep dokter.',
                'efek_samping' => 'Sembelit atau diare ringan, mulut kering.',
                'kontraindikasi' => 'Alergi bahan aktif; hati-hati pada gangguan ginjal.',
            ],
            'KAT-05' => [
                'indikasi' => 'Meredakan batuk, pilek, dan gejala flu.',
                'aturan_pakai' => 'Diminum 2-3 kali sehari sesuai anjuran pada kemasan.',
                'efek_samping' => 'Mengantuk, mulut kering, pusing ringan.',
                'kontraindikasi' => 'Hati-hati saat mengemudi; tidak untuk anak di bawah usia yang dianjurkan.',
            ],
            'KAT-06' => [
                'indikasi' => 'Meredakan gejala alergi seperti bersin, gatal, dan ruam.',
                'aturan_pakai' => '1 kali sehari atau sesuai anjuran.',
                'efek_samping' => 'Mengantuk, mulut kering, sakit kepala.',
                'kontraindikasi' => 'Alergi bahan aktif; hindari alkohol selama pemakaian.',
            ],
            'KAT-07' => [
                'indikasi' => 'Pengendalian tekanan darah, kolesterol, atau gangguan jantung sesuai resep.',
                'aturan_pakai' => 'Diminum teratur setiap hari pada jam yang sama.',
                'efek_samping' => 'Pusing, lemas, batuk kering (tergantung jenis obat).',
                'kontraindikasi' => 'Hamil, riwayat alergi bahan aktif, tekanan darah sangat rendah.',
            ],
            'KAT-08' => [
                'indikasi' => 'Pengendalian kadar gula darah pada diabetes melitus tipe 2.',
                'aturan_pakai' => 'Diminum sesuai resep, umumnya bersamaan atau sesudah makan.',
                'efek_samping' => 'Gula darah terlalu rendah, mual, gangguan pencernaan.',
                'kontraindikasi' => 'Gangguan ginjal atau hati berat; konsultasikan jika sedang hamil.',
            ],
            'KAT-09' => [
                'indikasi' => 'Pengobatan luar untuk infeksi, iritasi, atau peradangan kulit ringan.',
                'aturan_pakai' => 'Oleskan tipis pada kulit yang bersih 1-3 kali sehari.',
                'efek_samping' => 'Kulit perih, kemerahan, atau gatal.',
                'kontraindikasi' => 'Jangan dipakai pada luka terbuka luas atau area mata.',
            ],
            'KAT-10' => [
                'indikasi' => 'Pengobatan luar untuk iritasi atau infeksi mata dan telinga.',
                'aturan_pakai' => 'Teteskan sesuai petunjuk, jangan menyentuh ujung botol.',
                'efek_samping' => 'Perih sementara, penglihatan kabur sesaat.',
                'kontraindikasi' => 'Hentikan jika muncul iritasi berat; hindari memakai lensa kontak kecuali dianjurkan.',
            ],
        ];

        $catatan = [
            'b' => 'Dapat dibeli tanpa resep.',
            't' => 'Perhatikan tanda peringatan pada kemasan.',
            'k' => 'Obat keras: hanya dilayani dengan resep dokter yang sudah diverifikasi apoteker.',
        ];

        $golMap = ['b' => 'bebas', 't' => 'bebas_terbatas', 'k' => 'keras'];

        // ---------- 100 obat: [nama, kategori, golongan, satuan, harga beli] ----------
        $daftar = [
            // Analgesik
            ['Paracetamol 500 mg', 'KAT-01', 'b', 'strip', 2500],
            ['Paracetamol Sirup 120 mg/5 ml', 'KAT-01', 'b', 'botol', 8000],
            ['Ibuprofen 200 mg', 'KAT-01', 't', 'strip', 4000],
            ['Asetosal 80 mg', 'KAT-01', 't', 'strip', 3500],
            ['Asam Mefenamat 500 mg', 'KAT-01', 'k', 'strip', 5000],
            ['Natrium Diklofenak 50 mg', 'KAT-01', 'k', 'strip', 6000],
            ['Ketoprofen 100 mg', 'KAT-01', 'k', 'strip', 9000],
            ['Meloxicam 15 mg', 'KAT-01', 'k', 'strip', 7000],
            ['Ibuprofen 400 mg', 'KAT-01', 'k', 'strip', 7500],
            ['Piroksikam 20 mg', 'KAT-01', 'k', 'strip', 8000],
            // Antibiotik
            ['Amoxicillin 250 mg', 'KAT-02', 'k', 'strip', 6000],
            ['Cefadroxil 500 mg', 'KAT-02', 'k', 'strip', 15000],
            ['Ciprofloxacin 500 mg', 'KAT-02', 'k', 'strip', 12000],
            ['Azithromycin 500 mg', 'KAT-02', 'k', 'strip', 25000],
            ['Cotrimoxazole 480 mg', 'KAT-02', 'k', 'strip', 5000],
            ['Clindamycin 300 mg', 'KAT-02', 'k', 'strip', 18000],
            ['Metronidazole 500 mg', 'KAT-02', 'k', 'strip', 6500],
            ['Doxycycline 100 mg', 'KAT-02', 'k', 'strip', 7000],
            ['Cefixime 100 mg', 'KAT-02', 'k', 'strip', 20000],
            ['Ampicillin 500 mg', 'KAT-02', 'k', 'strip', 9000],
            // Vitamin
            ['Vitamin C 1000 mg', 'KAT-03', 'b', 'strip', 12000],
            ['Vitamin B Kompleks', 'KAT-03', 'b', 'strip', 5000],
            ['Vitamin D3 1000 IU', 'KAT-03', 'b', 'botol', 45000],
            ['Vitamin E 400 IU', 'KAT-03', 'b', 'botol', 38000],
            ['Multivitamin Dewasa', 'KAT-03', 'b', 'botol', 55000],
            ['Multivitamin Anak Sirup', 'KAT-03', 'b', 'botol', 32000],
            ['Kalsium dan Vitamin D', 'KAT-03', 'b', 'botol', 40000],
            ['Zinc 20 mg', 'KAT-03', 't', 'strip', 9000],
            ['Asam Folat 400 mcg', 'KAT-03', 't', 'strip', 4500],
            ['Vitamin B12 500 mcg', 'KAT-03', 'b', 'strip', 8000],
            // Pencernaan
            ['Antasida Tablet Kunyah', 'KAT-04', 'b', 'strip', 3000],
            ['Antasida Sirup', 'KAT-04', 'b', 'botol', 9000],
            ['Omeprazole 20 mg', 'KAT-04', 'k', 'strip', 9000],
            ['Lansoprazole 30 mg', 'KAT-04', 'k', 'strip', 14000],
            ['Domperidone 10 mg', 'KAT-04', 'k', 'strip', 6000],
            ['Loperamide 2 mg', 'KAT-04', 't', 'strip', 3500],
            ['Oralit Sachet', 'KAT-04', 'b', 'sachet', 1500],
            ['Bisacodyl 5 mg', 'KAT-04', 't', 'strip', 3000],
            ['Simetikon 80 mg', 'KAT-04', 'b', 'strip', 6000],
            ['Probiotik Sachet', 'KAT-04', 'b', 'box', 30000],
            // Batuk dan Flu
            ['Obat Batuk Hitam Sirup', 'KAT-05', 'b', 'botol', 7000],
            ['Ambroxol 30 mg', 'KAT-05', 't', 'strip', 3500],
            ['Bromhexine 8 mg', 'KAT-05', 't', 'strip', 3000],
            ['Gliseril Guaiakolat 100 mg', 'KAT-05', 'b', 'strip', 3500],
            ['Dextromethorphan 15 mg', 'KAT-05', 't', 'strip', 3000],
            ['Pseudoefedrin dan Triprolidin', 'KAT-05', 't', 'strip', 5000],
            ['Tablet Flu Paracetamol Fenilefrin', 'KAT-05', 't', 'strip', 4000],
            ['Salbutamol 2 mg', 'KAT-05', 'k', 'strip', 3000],
            ['Acetylcysteine 200 mg', 'KAT-05', 'k', 'strip', 11000],
            ['Permen Pelega Tenggorokan', 'KAT-05', 'b', 'box', 9000],
            // Alergi
            ['Cetirizine 10 mg', 'KAT-06', 't', 'strip', 4500],
            ['Loratadine 10 mg', 'KAT-06', 't', 'strip', 5000],
            ['Chlorpheniramine Maleat 4 mg', 'KAT-06', 't', 'strip', 1500],
            ['Fexofenadine 120 mg', 'KAT-06', 'k', 'strip', 16000],
            ['Levocetirizine 5 mg', 'KAT-06', 'k', 'strip', 9000],
            ['Ketotifen 1 mg', 'KAT-06', 'k', 'strip', 6000],
            ['Dexamethasone 0,5 mg', 'KAT-06', 'k', 'strip', 2500],
            ['Methylprednisolone 4 mg', 'KAT-06', 'k', 'strip', 8000],
            ['Desloratadine 5 mg', 'KAT-06', 'k', 'strip', 14000],
            ['Cetirizine Sirup 5 mg/5 ml', 'KAT-06', 't', 'botol', 20000],
            // Kardiovaskular
            ['Amlodipine 5 mg', 'KAT-07', 'k', 'strip', 4000],
            ['Amlodipine 10 mg', 'KAT-07', 'k', 'strip', 6000],
            ['Captopril 25 mg', 'KAT-07', 'k', 'strip', 3000],
            ['Lisinopril 10 mg', 'KAT-07', 'k', 'strip', 7000],
            ['Candesartan 8 mg', 'KAT-07', 'k', 'strip', 17000],
            ['Bisoprolol 5 mg', 'KAT-07', 'k', 'strip', 9000],
            ['Simvastatin 20 mg', 'KAT-07', 'k', 'strip', 8000],
            ['Atorvastatin 20 mg', 'KAT-07', 'k', 'strip', 12000],
            ['Furosemide 40 mg', 'KAT-07', 'k', 'strip', 2500],
            ['Clopidogrel 75 mg', 'KAT-07', 'k', 'strip', 15000],
            // Antidiabetes
            ['Metformin 500 mg', 'KAT-08', 'k', 'strip', 3500],
            ['Glimepiride 2 mg', 'KAT-08', 'k', 'strip', 6000],
            ['Glibenclamide 5 mg', 'KAT-08', 'k', 'strip', 2500],
            ['Gliclazide 80 mg', 'KAT-08', 'k', 'strip', 7000],
            ['Acarbose 50 mg', 'KAT-08', 'k', 'strip', 13000],
            ['Pioglitazone 30 mg', 'KAT-08', 'k', 'strip', 11000],
            ['Metformin XR 750 mg', 'KAT-08', 'k', 'strip', 9000],
            ['Vildagliptin 50 mg', 'KAT-08', 'k', 'strip', 28000],
            ['Sitagliptin 50 mg', 'KAT-08', 'k', 'strip', 32000],
            ['Glimepiride 4 mg', 'KAT-08', 'k', 'strip', 10000],
            // Obat Kulit
            ['Hydrocortisone Krim 1%', 'KAT-09', 't', 'tube', 9000],
            ['Miconazole Krim 2%', 'KAT-09', 't', 'tube', 11000],
            ['Ketoconazole Krim 2%', 'KAT-09', 'k', 'tube', 13000],
            ['Povidone Iodine 10%', 'KAT-09', 'b', 'botol', 9000],
            ['Gentamicin Salep 0,1%', 'KAT-09', 'k', 'tube', 6000],
            ['Asam Fusidat Krim 2%', 'KAT-09', 'k', 'tube', 30000],
            ['Salep Kulit Antiseptik', 'KAT-09', 'b', 'tube', 7000],
            ['Bedak Salisil', 'KAT-09', 'b', 'botol', 6000],
            ['Clotrimazole Krim 1%', 'KAT-09', 't', 'tube', 8000],
            ['Kalamin Losion', 'KAT-09', 'b', 'botol', 12000],
            // Obat Mata dan Telinga
            ['Natrium Klorida Tetes Mata', 'KAT-10', 't', 'botol', 12000],
            ['Chloramphenicol Tetes Mata 0,5%', 'KAT-10', 'k', 'botol', 10000],
            ['Tobramycin Tetes Mata', 'KAT-10', 'k', 'botol', 28000],
            ['Polyvinyl Alcohol Tetes Mata', 'KAT-10', 't', 'botol', 15000],
            ['Naphazoline Tetes Mata', 'KAT-10', 't', 'botol', 9000],
            ['Ofloxacin Tetes Telinga', 'KAT-10', 'k', 'botol', 35000],
            ['Carbamide Peroxide Tetes Telinga', 'KAT-10', 't', 'botol', 22000],
            ['Dexamethasone Tetes Mata', 'KAT-10', 'k', 'botol', 14000],
            ['Hypromellose Tetes Mata', 'KAT-10', 't', 'botol', 18000],
            ['Gentamicin Tetes Mata', 'KAT-10', 'k', 'botol', 16000],
        ];

        // Kolom info detail hanya diisi kalau ALTER TABLE obat sudah dijalankan
        $punyaInfo = Schema::hasColumn('obat', 'catatan_apoteker');

        foreach ($daftar as $i => [$nama, $kodeKat, $gol, $satuan, $beli]) {
            $kode = 'OBT-' . str_pad($i + 101, 3, '0', STR_PAD_LEFT); // OBT-101 s/d OBT-200
            $minimum = mt_rand(10, 25);

            // Beberapa obat sengaja stoknya menipis dan hampir kedaluwarsa supaya dashboard ada isinya
            $stok = ($i % 13 === 5) ? mt_rand(1, $minimum) : mt_rand($minimum + 10, 220);
            $expired = ($i % 17 === 8)
                ? now()->addDays(mt_rand(5, 25))
                : now()->addDays(mt_rand(120, 1000));

            $jual = (int) (round($beli * (1.3 + mt_rand(0, 15) / 100) / 500) * 500);

            $row = [
                'kode_obat' => $kode,
                'nama_obat' => $nama,
                'id_kategori' => $idKategori[$kodeKat],
                'id_supplier' => $supplierIds[$i % count($supplierIds)],
                'golongan' => $golMap[$gol],
                'satuan' => $satuan,
                'harga_beli' => $beli,
                'harga_jual' => $jual,
                'stok' => $stok,
                'stok_minimum' => $minimum,
                'tanggal_expired' => $expired->toDateString(),
            ];

            if ($punyaInfo) {
                $row += $info[$kodeKat] + [
                    'deskripsi' => $nama . ', kategori ' . $kategori[$kodeKat][0] . '. Dijual per ' . $satuan . '.',
                    'catatan_apoteker' => $catatan[$gol],
                ];
            }

            DB::table('obat')->updateOrInsert(['kode_obat' => $kode], $row);
        }

        $this->command->info('Selesai: ' . count($daftar) . ' obat, ' . count($kategori) . ' kategori, dan supplier tambahan.');
    }
}