<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function export(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    // Query data berdasarkan filter tanggal
    $query = Penjualan::query();
    if ($fromDate) {
        $query->whereDate('tgl', '>=', $fromDate);
    }
    if ($toDate) {
        $query->whereDate('tgl', '<=', $toDate);
    }
    $penjualan = $query->orderBy('tgl', 'desc')->get();

    // Buat file Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No')
        ->setCellValue('B1', 'Kasir')
        ->setCellValue('C1', 'Tanggal & Waktu')
        ->setCellValue('D1', 'Pelanggan')
        ->setCellValue('E1', 'Tipe Pelanggan')
        ->setCellValue('F1', 'Total Pembelanjaan')
        ->setCellValue('G1', 'Diskon (Rp)')
        ->setCellValue('H1', 'Poin Digunakan')
        ->setCellValue('I1', 'Total Akhir Pembelanjaan');

    // Data
    $row = 2;
    foreach ($penjualan as $index => $item) {
        $sheet->setCellValue("A{$row}", $index + 1)
            ->setCellValue("B{$row}", $item->user->name)
            ->setCellValue("C{$row}", $item->tgl)
            ->setCellValue("D{$row}", $item->member_id ? $item->member->nama : 'Non Membership')
            ->setCellValue("E{$row}", $item->member_id ? ($item->member->type == 1 ? 'Bronze' : ($item->member->type == 2 ? 'Silver' : 'Gold')) : '-')
            ->setCellValue("F{$row}", $item->total_harga)
            ->setCellValue("G{$row}", $item->diskon)
            ->setCellValue("H{$row}", $item->used_poin ?? 0)
            ->setCellValue("I{$row}", $item->total_harga - $item->diskon);
        $row++;
    }

    // Nama file lebih informatif
    $filename = 'Laporan_Penjualan_' . ($fromDate ? $fromDate : 'Semua') . '_sampai_' . ($toDate ? $toDate : 'Semua') . '.xlsx';

    // Simpan dan kirim sebagai response
    $writer = new Xlsx($spreadsheet);
    $response = new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    });

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}
}
