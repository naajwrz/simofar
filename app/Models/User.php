<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = ['id_role', 'nama_staf', 'email', 'password', 'status'];
    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id_user', 'id_user');
    }

    private function punyaRole(string $nama): bool
    {
        return $this->role && strtolower(trim($this->role->nama_role)) === $nama;
    }

    public function isAdmin(): bool { return $this->punyaRole('admin'); }
    public function isApoteker(): bool { return $this->punyaRole('apoteker'); }
    public function isPelanggan(): bool { return $this->punyaRole('pelanggan'); }
}