<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';

    public $timestamps = false;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'no_telepon',
        'alamat',
        'status_kerjasama',
    ];

    public function pembelian()
    {
        return $this->hasMany(
            Pembelian::class,
            'id_supplier',
            'id_supplier'
        );
    }
}

