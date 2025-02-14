@extends('layouts.main')

@section('title', 'Tambah Member')

@section('content')
<div class="container">
    <h4 class="mt-4">Tambah Member</h4>

    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif

    <form action="{{ route('member.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" id="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="number" name="phone" class="form-control" id="phone" required>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Tambah Member</button>
        <a href="{{ url('/member') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
    </form>
</div>
@endsection
