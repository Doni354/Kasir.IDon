@extends('layouts.main')

@section('title', 'Edit Produk')

@section('content')
<div class="container">
    <h4 class="mt-4">Edit Produk</h4>

    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif

    <form action="{{ route('produk.update', $produk->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $produk->name }}" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="number" name="price" class="form-control" id="price" value="{{ $produk->price }}" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" id="category_id" class="form-control select2" required>
                <option value="" selected disabled>Memuat data kategori</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ url('/produk') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
@section('scripts')
    <!-- Tambahkan Select2 dan jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: "Pilih Category",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('categories.search') }}",
                    dataType: 'json',
                    delay: 250,
                    beforeSend: function() {
                        $('#category_id').html('<option value="" disabled selected>Memuat data...</option>');
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Jika sudah ada kategori yang dipilih sebelumnya, pastikan tetap muncul
            var selectedCategoryId = "{{ $produk->category_id }}";
            var selectedCategoryName = "{{ $produk->kategori->name ?? '' }}";
            if (selectedCategoryId) {
                var newOption = new Option(selectedCategoryName, selectedCategoryId, true, true);
                $('#category_id').append(newOption).trigger('change');
            }
        });
    </script>
@endsection
