<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggans = Pelanggan::query()
            ->leftJoin('users', 'users.id_user', '=', 'pelanggan.id_user')
            ->select('pelanggan.*', 'users.email', 'users.status as status_akun')
            ->selectSub(function ($q) {
                $q->from('penjualan')
                    ->whereColumn('penjualan.id_pelanggan', 'pelanggan.id_pelanggan')
                    ->where('status_pembayaran', 'lunas')
                    ->selectRaw('COUNT(*)');
            }, 'jumlah_transaksi')
            ->selectSub(function ($q) {
                $q->from('penjualan')
                    ->whereColumn('penjualan.id_pelanggan', 'pelanggan.id_pelanggan')
                    ->where('status_pembayaran', 'lunas')
                    ->selectRaw('COALESCE(SUM(total_tagihan), 0)');
            }, 'total_belanja')
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = '%' . $request->q . '%';
                $q->where(function ($w) use ($kw) {
                    $w->where('pelanggan.nama_pelanggan', 'like', $kw)
                        ->orWhere('pelanggan.no_telepon', 'like', $kw)
                        ->orWhere('users.email', 'like', $kw);
                });
            })
            ->orderBy('pelanggan.nama_pelanggan')
            ->get();

        return view('admin.pelanggan', compact('pelanggans'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:500',
            'status_akun' => 'nullable|in:aktif,nonaktif',
        ]);

        $pelanggan->update([
            'nama_pelanggan' => $data['nama_pelanggan'],
            'no_telepon' => $data['no_telepon'] ?? null,
            'alamat' => $data['alamat'] ?? null,
        ]);

        if ($pelanggan->id_user) {
            $perubahan = ['nama_staf' => $data['nama_pelanggan']];
            if (!empty($data['status_akun'])) {
                $perubahan['status'] = $data['status_akun'];
            }
            User::where('id_user', $pelanggan->id_user)->update($perubahan);
        }

        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }
}