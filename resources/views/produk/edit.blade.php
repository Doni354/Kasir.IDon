@extends('layouts.main')
@section( 'title', 'Edit Produk')
@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit Produk</h6>
            <form action="/edit-produk/{{ $produk->id }}" method="POST">
                @method('put')
                @csrf
                <div class="mb-3">
                    <label for="y" class="form-label">Kode Produk</label>
                    <input type="text" name="kode_produk" value="{{ $produk->kode_produk }}" class="form-control" id="y" disabled>
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Nama Produk</label>
                    <input type="text" name="nama" value="{{ $produk->nama }}" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Harga</label>
                    <input type="number" name="harga" value="{{ $produk->harga }}" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Stok</label>
                    <input type="number" name="stok" value="{{ $produk->stok }}" class="form-control" id="y">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection