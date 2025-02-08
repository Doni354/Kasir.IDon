@extends('layouts.main')

@section('title', 'Tambah Category')

@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Tambah Category</h6>
            <form action="/tambah-category" method="POST" id="userForm">
                @csrf
                <!-- Nama -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Category</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="nameFeedback" class="valid-feedback d-none">Nama Category tersedia!</div>
                    <div id="nameErrorFeedback" class="invalid-feedback d-none">Nama Category sudah digunakan!</div>
                </div>



                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>


    <!-- JavaScript Validation -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const nameInput = document.getElementById("name");

        // Feedback divs
        const nameFeedback = document.getElementById("nameFeedback");
        const nameErrorFeedback = document.getElementById("nameErrorFeedback");


        // Validasi Nama (cek unik di database)
        nameInput.addEventListener("input", function () {
            fetch(`/check-category?name=${nameInput.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        nameInput.classList.add("is-invalid");
                        nameInput.classList.remove("is-valid");
                        nameFeedback.classList.add("d-none");
                        nameErrorFeedback.classList.remove("d-none");
                    } else {
                        nameInput.classList.remove("is-invalid");
                        nameInput.classList.add("is-valid");
                        nameFeedback.classList.remove("d-none");
                        nameErrorFeedback.classList.add("d-none");
                    }
                });
        });

    });
    </script>
    <!-- Custom CSS -->
    
@endsection
