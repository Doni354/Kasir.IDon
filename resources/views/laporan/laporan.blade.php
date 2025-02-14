@extends('layouts.main')
@section( 'title', 'Laporan Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Laporan Penjualan</h6>
        <div class="table-responsive">
            <form action="/cari" method="POST">
                <div class="d-flex col-6 mb-3">
                    @csrf
                    <input type="date" value="{{ $tanggal ?? "" }}" name="tanggal" class="form-control">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Total Bayar</th>
                        <th scope="col">Kasir</th>
                        <th scope="col">*Set</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $item)
                        <tr>
                            <td >{{ $loop->iteration }}</td>
                            <td>{{ $item->tgl }}</td>
                            <td>{{ $item->kode_penjualan }}</td>
                            <td>Rp. {{ number_format($item->total_harga) }}</td>
                            <td>Rp. {{ number_format($item->bayar) }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>
                                <a class="btn btn-success btn-sm" href="/detail={{ $item->kode_penjualan }}"><i class="fa fa-search"></i> Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
