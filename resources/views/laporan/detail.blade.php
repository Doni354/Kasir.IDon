@extends('layouts.main')
@section( 'title', 'Detail Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row mb-3 p-4 bg-light">
        <div class="col-6">
            <ul class="list-unstyled">
                <li><strong>Member:</strong> {{ $penjualan->member_id ? $penjualan->member->nama : 'Non Membership' }}</li>
                <li><strong>Poin:</strong> {{ $penjualan->member_id ? $penjualan->member->poin : '-' }}</li>
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
                        <th scope="col">Jumlah</th>
                        <th scope="col">Harga</th>

                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailPenjualan as $item)
                    @php
                        $hargaDasar = $item->produk->price;
                        $markup = ($item->harga_satuan / 1.12) - $hargaDasar;
                        $hargaSetelahMarkup = $hargaDasar + $markup;
                        $ppn = $hargaSetelahMarkup * 12 / 100;
                        $hargaAkhir = $hargaSetelahMarkup + $ppn;
                        $subtotal = $hargaAkhir * $item->qty;
                    @endphp
                        <tr>
                            <td >{{ $loop->iteration }}</td>
                            <td>{{ $item->produk->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp. {{ number_format($hargaAkhir) }}</td>

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
