@extends('layouts.main')
@section( 'title', 'Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data Penjualan</h6>
        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-end mb-3">
                <form action="/mulai-penjualan" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Mulai Penjualan
                    </button>
                </form>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal"><i class="fa fa-plus"></i> Pilih Pelanggan</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Total Bayar</th>
                        <th scope="col">Kasir</th>
                        <th scope="col">Opsi</th>
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
                                <a class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus penjualan ini?')" href="/hapus-penjualan/{{ $item->kode_penjualan }}"><i class="fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
