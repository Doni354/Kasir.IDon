<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(){
        $penjualan = Penjualan::latest()->get();
        return view('/laporan.laporan', compact('penjualan'));
    }

    public function search(Request $request){
        $tanggal = $request->input('tanggal');
        $penjualan = Penjualan::whereDate('tgl', $tanggal)->get();
        return view('/laporan.laporan', compact('penjualan', 'tanggal'));
    }

    public function show(Penjualan $penjualan){
        $detailPenjualan = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();
        return view('/laporan.detail', compact('penjualan', 'detailPenjualan'));
    }
}
