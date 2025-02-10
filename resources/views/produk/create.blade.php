@extends('layouts.main')

@section('title', 'Tambah Produk')

@section('content')
<div class="container">
    <h4 class="mt-4">Tambah Produk</h4>

    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif

    <form action="{{ route('produk.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="number" name="price" class="form-control" id="price" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" id="category_id" class="form-control select2" required>
                <option value="" selected disabled>Memuat data kategori...</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Produk</button>
        <a href="{{ url('/produk') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
    <!-- Tambahkan Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: "Cari dan Pilih Kategori",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('categories.search') }}", // Endpoint untuk mencari kategori
                    dataType: 'json',
                    delay: 250, // Untuk mengurangi beban server
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return { id: item.id, text: item.name };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection
