<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';

   protected $fillable = [
    'kode_obat', 'nama_obat', 'id_kategori', 'id_supplier', 'golongan',
    'satuan', 'harga_beli', 'harga_jual', 'stok', 'stok_minimum', 'tanggal_expired',
    'deskripsi', 'indikasi', 'aturan_pakai', 'efek_samping',
    'kontraindikasi', 'catatan_apoteker',
];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(
            KategoriObat::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class,
            'id_supplier',
            'id_supplier'
        );
    }
}