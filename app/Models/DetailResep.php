<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailResep extends Model
{
    protected $table = 'detail_resep';

    protected $primaryKey = 'id_detail_resep';

    public $timestamps = false;

    protected $guarded = [];

    public function resep()
    {
        return $this->belongsTo(
            Resep::class,
            'id_resep',
            'id_resep'
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