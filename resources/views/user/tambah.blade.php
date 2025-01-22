@extends('layouts.main')
@section( 'title', 'Tambah Kasir')
@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Tambah Kasir</h6>
            <form action="/tambah-user" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="y" class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="y">
                </div>
                <div class="form-floating mb-4">
                    <select name="role" class="form-select">
                        <option value="petugas">Petugas</option>
                        <option value="kasir">Kasir</option>
                    </select>
                    <label for="floatingPassword">Role</label>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
