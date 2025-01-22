@extends('layouts.main')
@section( 'title', 'Detail Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row mb-3 p-4 bg-light">
        <div class="col-6">
            <ul class="list-unstyled">
                <li><strong>Pelanggan : {{ $penjualan->pelanggan->nama }}</strong></li>
                <li><strong>Telp : {{ $penjualan->pelanggan->telp }}</strong></li>
                <li><strong>Alamat : {{ $penjualan->pelanggan->alamat }}</strong></li>
            </ul>
        </div>
        <div class="col-6 text-end">
            <ul class="list-unstyled">
                <li><strong>Kode Transaksi : {{ $penjualan->kode_penjualan }}</strong></li>
                <li><strong>Tanggal Transaksi : {{ $penjualan->tgl }}</strong></li>
            </ul>
        </div>
    </div>
    <div class="row g-4">
        <h6 class="mb-4">Detail Penjualan</h6>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Produk</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailPenjualan as $item)
                        <tr>
                            <td >{{ $loop->iteration }}</td>
                            <td>{{ $item->produk->nama }}</td>
                            <td>Rp. {{ number_format($item->produk->harga) }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp. {{ number_format($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-12 text-end">
                <ul class="list-unstyled">
                    <li><strong>Total Harga : {{ number_format($penjualan->total_harga) }}</strong></li>
                    <li><strong>Total Bayar : {{ number_format($penjualan->bayar) }}</strong></li>
                    <li><strong>Total Kembalian : {{ number_format($penjualan->bayar - $penjualan->total_harga) }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection