@extends('layouts.main')
@section( 'title', 'Tambah Pelanggan')
@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Tambah Pelanggan</h6>
            <form action="/tambah-pelanggan" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="y" class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Telp</label>
                    <input type="number" name="telp" class="form-control" id="y">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection