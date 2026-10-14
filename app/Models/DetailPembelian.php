<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id_detail_pembelian';
    public $timestamps = false;

    protected $fillable = [
        'id_pembelian',
        'id_obat',
        'jumlah',
        'harga_beli',
        'subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(
            Pembelian::class,
            'id_pembelian',
            'id_pembelian'
        );
    }

    public function obat()
    {
        return $this->belongsTo(
            Obat::class,
            'id_obat',
            'id_obat'
        )->withTrashed();
    }
}