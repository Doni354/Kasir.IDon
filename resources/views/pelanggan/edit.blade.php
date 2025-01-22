@extends('layouts.main')
@section( 'title', 'Edit Pelanggan')
@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit Pelanggan</h6>
            <form action="/edit-pelanggan/{{ $pelanggan->id }}" method="POST">
                @csrf
                @method('put')
                <div class="mb-3">
                    <label for="y" class="form-label">Nama</label>
                    <input type="text" name="nama" value="{{ $pelanggan->nama }}" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3">{{ $pelanggan->alamat }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Telp</label>
                    <input type="number" value="{{ $pelanggan->telp }}" name="telp" class="form-control" id="y">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection