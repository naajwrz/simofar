<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPembelian;

class Pembelian extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel tunggal di phpMyAdmin
    protected $table = 'pembelian';

    // Sesuaikan primary key-nya
    protected $primaryKey = 'id_pembelian';

public $timestamps = false;



    protected $guarded = [];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function details()
{
    return $this->hasMany(
        DetailPembelian::class,
        'id_pembelian',
        'id_pembelian'
    );
}

    // Relasi ke User / Staf
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Detail Pembelian
    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'id_pembelian', 'id_pembelian');
    }
}