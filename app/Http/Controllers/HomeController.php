<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Produk;

class HomeController extends Controller
{
    public function index()
    {
        // --- Grafik Transaksi Harian ---
        // Ambil transaksi harian dengan mengelompokkan berdasarkan tanggal dan jumlahkan total_harga
        $transaksi = Penjualan::selectRaw('DATE(tgl) as tanggal, SUM(total_harga) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $transLabels = $transaksi->pluck('tanggal')->toArray();
        $transData   = $transaksi->pluck('total')->toArray();

        // --- Data Barang Re-Order ---
        // Misal threshold reorder: stok < 10
        $reorderProducts = Produk::all()->filter(function ($p) {
            return $p->totalStok() < 10;
        });

        $reorderLabels = $reorderProducts->pluck('name')->toArray();
        $reorderData   = $reorderProducts->map(function ($p) {
            return $p->totalStok();
        })->toArray();

        return view('home', compact('transLabels', 'transData', 'reorderProducts', 'reorderLabels', 'reorderData'));
    }
}
