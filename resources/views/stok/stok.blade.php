@extends('layouts.main')
@section('title', 'Stok')
@section('content')
<div class="container">
    <div class="row g-4">
        <h6 class="mb-4">Data Stok</h6>
        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(auth()->user()->role == 'admin')
            <div class="d-flex justify-content-end mb-3">
                <a href="/stok-tambah" class="btn btn-primary"><i class="fa fa-plus"></i> tambah Stok</a>
            </div>
            @endif
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Stok</th>
                <th>Expired Date</th>
                <th>Buy Date</th>

                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                    <th scope="col">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($stokList as $index => $stok)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $stok->product->name ?? 'Tidak Ada' }}</td>
                    <td>{{ $stok->qty }}</td>
                    <td>{{ $stok->expired_date ? date('d-m-Y', strtotime($stok->expired_date)) : '-' }}</td>
                    <td>{{ $stok->buy_date ? date('d-m-Y', strtotime($stok->buy_date)) : '-' }}</td>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                        <td>
                            <a href="#" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
