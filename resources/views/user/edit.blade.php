@extends('layouts.main')
@section( 'title', 'Edit User')
@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit User</h6>
            <form action="/edit-user/{{ $user->id }}" method="POST">
                @method('put')
                @csrf
                <div class="mb-3">
                    <label for="y" class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" id="y">
                </div>
                <div class="mb-3">
                    <label for="y" class="form-label">Password</label>
                    <input type="password" name="password" value="{{ $user->password }}" class="form-control" id="y">
                </div>
                <div class="form-floating mb-4">
                    <select name="role" class="form-select">
                        <option value="petugas">Petugas</option>
                        <option value="kasir">Kasir</option>
                    </select>
                    <label for="floatingPassword">Role</label>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
