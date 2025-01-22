<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(){
        $penjualan = Penjualan::latest()->get();
        return view('/penjualan.penjualan', compact('penjualan'));
    }

    public function mulai(Request $request){
        $penjualan = Penjualan::create([
            'user_id' => $request->user_id,
            'tgl' => now(),
            'total_harga' => 0,
            'bayar' => 0,
        ]);
        return redirect()->route('proses.penjualan', ['kode_penjualan' => $penjualan->kode_penjualan]);
    }

    public function proses($kode_penjualan){
        $penjualan = Penjualan::where('kode_penjualan', $kode_penjualan)->first();
        $produk = Produk::all();
        $keranjang = DetailPenjualan::whereIn('kode_penjualan', [$kode_penjualan])->get();
        return view('/penjualan.proses', compact('penjualan', 'produk', 'keranjang'));
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'qty' => 'required|min:1'
        ],[
            'qty.required' => 'Mohon isi qty'
        ]);

        $produk = Produk::find($request->produk_id);
        if($produk->stok < $request->qty){
            return back()->with('msg', 'Stok Tidak Mencukupi');
        }

        $detailPenjualan = new DetailPenjualan;
        $detailPenjualan->kode_penjualan = $request->kode_penjualan;
        $detailPenjualan->produk_id = $request->produk_id;
        $detailPenjualan->qty = $request->qty;
        $detailPenjualan->subtotal = $produk->harga * $request->qty;
        $detailPenjualan->save();

        return back()->with('msg', 'Produk Berhasil Ditambahkan ke Keranjang');
    }

    public function delete(DetailPenjualan $detailPenjualan){
        $detailPenjualan->delete();
        return back();
    }

    public function bayar(Penjualan $penjualan, Request $request){
        $request->validate([
            'bayar' => 'required'
        ]);

        $bayar = $request->bayar;
        $total_harga = $request->total_harga;
        $kembalian = $bayar - $total_harga;
        if($bayar < $total_harga){
            return back()->with('msg', 'Masukkan uang yang cukup');
        }

        $penjualan->bayar = $bayar; // Update field bayar
        $penjualan->total_harga = $total_harga; // Update field total harga
        $detailPenjualan = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();

        // Update stok
        foreach($detailPenjualan as $data){
            $produk = Produk::find($data->produk_id);
            $produk->decrement('stok', $data->qty);
        }

        $penjualan->update();

        return back()->with([
            'msg' => 'Pembayaran Berhasil',
            'status' => true,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
        ]);
    }

    public function nota(Penjualan $penjualan){
        $detail = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();
        return view('/penjualan.nota', compact('penjualan', 'detail'));
    }

    public function deletePenjualan(Penjualan $penjualan){
        $detailPenjualan = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();
        foreach($detailPenjualan as $data){
            $data->delete();
        }

        $penjualan->delete();
        return back()->with('msg', 'Semua data penjualan ini berhasil dihapus');
    }
}
