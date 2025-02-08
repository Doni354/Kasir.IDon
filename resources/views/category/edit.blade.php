@extends('layouts.main')

@section('title', 'Edit Category')

@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit Category</h6>
            <form action="/edit-category/{{ $category->id }}" method="POST" id="editUserForm">
                @method('put')
                @csrf

                <!-- Nama -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" id="name">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="nameFeedback" class="valid-feedback d-none">Nama Category tersedia!</div>
                    <div id="nameErrorFeedback" class="invalid-feedback d-none">Nama Category sudah digunakan!</div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</div>

@endsection
