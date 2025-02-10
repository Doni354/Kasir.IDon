@extends('layouts.main')
@section( 'title', 'Produk')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data Produk</h6>
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

            @if(auth()->user()->role == 'admin')
            <div class="d-flex justify-content-end mb-3">
                <a href="/produk-tambah" class="btn btn-primary" ><i class="fa fa-plus"></i> Produk Baru</a>
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Kategori</th>

                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <th scope="col">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>

                        <tr>
                    @foreach($produk as $p)
                        <td >{{ $loop->iteration }}</td>
                        <td>{{ $p->name }}</td>
                        <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>{{ $p->kategori->name ?? '-' }}</td>

                            {{-- <td>
                                @if($item->stok <= 5 && $item->stok > 0)
                                    {{ $item->stok }}
                                    <span class="text-sm text-danger"> *</span><i>Stok Menipis</i>
                                @elseif($item->stok == 0)
                                    <span class="text-sm text-danger"> *</span><i>Stok Habis</i>
                                @else {{ $item->stok }}
                                @endif
                            </td> --}}
                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <td>
                                <a href="{{ url('/edit-produk='.$p->id) }}" class="btn btn-warning">Edit</a>
                                <a href="{{ url('/hapus-produk/'.$p->id) }}" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
