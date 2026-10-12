<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriObat extends Model
{
    protected $table = 'kategori_obat';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'keterangan'
    ];

    public $timestamps = false;

    public function obat()
    {
        return $this->hasMany(Obat::class, 'id_kategori', 'id_kategori');
    }
}