<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Stok;
Use App\Models\Member;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(){
        $member = Member::where('status', 1)->latest()->get();
        $penjualan = Penjualan::latest()->get();
        return view('/penjualan.penjualan', compact('member', 'penjualan'));
    }


    public function mulai(Request $request){
        $penjualan = Penjualan::create([
            'user_id' => $request->user_id,
            'member_id' => $request->member_id ?? null, // Bisa null jika tidak memilih
            'tgl' => now(),
            'total_harga' => 0,
            'bayar' => 0,
        ]);
        return redirect()->route('proses.penjualan', ['kode_penjualan' => $penjualan->kode_penjualan]);
    }


    public function proses($kode_penjualan)
    {
        $penjualan = Penjualan::with('member')->where('kode_penjualan', $kode_penjualan)->first();
        $produk = Produk::all();
        $keranjang = DetailPenjualan::where('kode_penjualan', $kode_penjualan)->get();
        return view('/penjualan.proses', compact('penjualan', 'produk', 'keranjang'));
    }

    public function hitungHargaJual($hargaDasar, $memberType)
{
    $persentaseMarkup = match ($memberType) {
        1 => 30,
        2 => 20,
        3 => 10,
        default => 30 // Non Membership
    };

    $hargaSetelahMarkup = $hargaDasar + ($hargaDasar * $persentaseMarkup / 100);
    $ppn = $hargaSetelahMarkup * 12 / 100;
    $hargaAkhir = $hargaSetelahMarkup + $ppn;

    return round($hargaAkhir); // Harga jual final setelah PPN
}
public function store(Request $request)
{
    $validatedData = $request->validate([
        'qty' => 'required|min:1'
    ],[
        'qty.required' => 'Mohon isi jumlah produk'
    ]);

    $produk = Produk::find($request->produk_id);
    $totalStok = $produk->totalStok();

    if ($totalStok < $request->qty) {
        return back()->with('msg', 'Stok Tidak Mencukupi');
    }

    $penjualan = Penjualan::where('kode_penjualan', $request->kode_penjualan)->first();
    $memberType = $penjualan->member ? $penjualan->member->type : null;

    $hargaJual = $this->hitungHargaJual($produk->price, $memberType);
    $markup = $hargaJual / 1.12 - $produk->price;

    $detailPenjualan = new DetailPenjualan;
    $detailPenjualan->kode_penjualan = $request->kode_penjualan;
    $detailPenjualan->produk_id = $request->produk_id;
    $detailPenjualan->qty = $request->qty;
    $detailPenjualan->harga_satuan = $hargaJual;
    $detailPenjualan->markup = $markup;
    $detailPenjualan->subtotal = $hargaJual * $request->qty;
    $detailPenjualan->save();

    $qtyDibutuhkan = $request->qty;
    $stokTerpakai = [];

    $stokProduk = Stok::where('product_id', $request->produk_id)
        ->where('expired_date', '>', now())
        ->orderBy('expired_date', 'asc')
        ->get();

    foreach ($stokProduk as $stok) {
        if ($qtyDibutuhkan <= 0) break;

        $stokTersedia = $stok->qty;
        $qtyDiambil = min($stokTersedia, $qtyDibutuhkan);

        $stok->decrement('qty', $qtyDiambil);
        $qtyDibutuhkan -= $qtyDiambil;

        $stokTerpakai[] = ['stok_id' => $stok->id, 'qty' => $qtyDiambil];
    }

    $detailPenjualan->stok_terpakai = json_encode($stokTerpakai);
    $detailPenjualan->save();

    return back()->with('msg', 'Produk Berhasil Ditambahkan ke Keranjang');
}

public function bayar(Penjualan $penjualan, Request $request)
{
    $request->validate(['bayar' => 'required']);

    $bayar = $request->bayar;
    $total_harga = $request->total_harga;
    $kembalian = $bayar - $total_harga;

    if ($bayar < $total_harga) {
        return back()->with('msg', 'Masukan uang yang cukup');
    }

    $penjualan->bayar = $bayar;
    $penjualan->total_harga = $total_harga;
    $penjualan->status = 'paid';

    if ($penjualan->member) {
        $member = $penjualan->member;

        // Tambah poin (misal: 1 poin per Rp1000 pembelian)
        $poinTambahan = floor($total_harga / 1000);
        $member->poin += $poinTambahan;
        $member->total_belanja = $member->total_belanja + $total_harga;

        // Update type member jika memenuhi syarat
        if ($member->total_belanja >= 5000000) {
            $member->type = 3;
        } elseif ($member->total_belanja >= 1000000) {
            $member->type = 2;
        }

        $member->save();

        session()->flash('poin_tambahan', $poinTambahan);
        session()->flash('poin_total', $member->poin);
    }

    $penjualan->update();

    return back()->with([
        'msg' => 'Pembayaran Berhasil',
        'status' => true,
        'bayar' => $bayar,
        'kembalian' => $kembalian,
    ]);
}







public function delete(DetailPenjualan $detailPenjualan)
{
    // Ambil data stok yang sebelumnya dikurangi
    $stokTerpakai = json_decode($detailPenjualan->stok_terpakai, true);

    if ($stokTerpakai) {
        foreach ($stokTerpakai as $stokItem) {
            $stok = Stok::find($stokItem['stok_id']);
            if ($stok) {
                $stok->increment('qty', $stokItem['qty']); // Kembalikan stok
            }
        }
    }

    // Hapus detail penjualan
    $detailPenjualan->delete();

    return back()->with('msg', 'Produk berhasil dihapus dan stok dikembalikan.');
}



public function applyDiscount($detail_id, $discount_id)
{
    // Ambil data detail penjualan dan diskon
    $detail = DetailPenjualan::findOrFail($detail_id);
    $discount = \App\Models\Discount::findOrFail($discount_id);

    // Pastikan diskon sesuai dengan produk di detail penjualan
    if ($detail->produk_id != $discount->product_id) {
        return redirect()->back()->with('msg', 'Diskon tidak sesuai dengan produk.');
    }

    // Ambil data penjualan dan member (jika ada)
    $penjualan = Penjualan::where('kode_penjualan', $detail->kode_penjualan)->first();
    $member = $penjualan ? $penjualan->member : null;

    // Jika diskon membutuhkan poin, pastikan member ada dan memiliki poin yang cukup
    if ($discount->needed_poin > 0) {
        if (!$member) {
            return redirect()->back()->with('msg', 'Diskon ini hanya berlaku untuk member.');
        }
        if ($member->poin < $discount->needed_poin) {
            return redirect()->back()->with('msg', 'Poin member tidak mencukupi untuk menggunakan diskon ini.');
        }
        // Kurangi poin member
        $member->decrement('poin', $discount->needed_poin);
    }

    // Hitung jumlah asli (tanpa diskon)
    $originalAmount = $detail->harga_satuan * $detail->qty;
    // Hitung potongan diskon (dalam rupiah)
    $discountAmount = ($discount->discount / 100) * $originalAmount;

    // Update detail penjualan
    $detail->update([
        'discount_id'      => $discount->id,
        'discount_applied' => true,
        'discount_amount'  => $discountAmount,
        'subtotal'         => $originalAmount - $discountAmount,
    ]);

    return redirect()->back()->with('msg', 'Diskon berhasil diterapkan.');
}

public function cancelDiscount($detail_id)
{
    $detail = DetailPenjualan::findOrFail($detail_id);

    if (!$detail->discount_applied) {
        return redirect()->back()->with('msg', 'Tidak ada diskon yang diterapkan.');
    }

    $discount = \App\Models\Discount::find($detail->discount_id);
    $penjualan = Penjualan::where('kode_penjualan', $detail->kode_penjualan)->first();
    $member = $penjualan ? $penjualan->member : null;

    // Kembalikan poin ke member jika diperlukan
    if ($discount && $discount->needed_poin > 0 && $member) {
        $member->increment('poin', $discount->needed_poin);
    }

    $originalAmount = $detail->harga_satuan * $detail->qty;

    $detail->update([
        'discount_id'      => null,
        'discount_applied' => false,
        'discount_amount'  => 0,
        'subtotal'         => $originalAmount,
    ]);

    return redirect()->back()->with('msg', 'Diskon telah dibatalkan dan poin dikembalikan.');
}




    public function nota(Penjualan $penjualan){
        $detail = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();
        return view('/penjualan.nota', compact('penjualan', 'detail'));
    }

    // ------------------------------------------------------

    public function deletePenjualan(Penjualan $penjualan){
        $detailPenjualan = DetailPenjualan::where('kode_penjualan', $penjualan->kode_penjualan)->get();
        foreach($detailPenjualan as $data){
            $data->delete();
        }

        $penjualan->delete();
        return back()->with('msg', 'Semua Data penjualan ini berhasil dihapus');
    }

}
