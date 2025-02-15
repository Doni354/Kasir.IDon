@extends('layouts.main')

@section('title', 'Tambah Diskon')

@section('content')
<div class="container">
    <h4 class="mt-4">Tambah Diskon</h4>

    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif

    <form action="{{ route('discount.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Diskon</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Diskon (%)</label>
            <input type="number" name="discount" class="form-control" id="discount" min="1" max="100" required>
        </div>

        <div class="mb-3">
            <label for="valid_until" class="form-label">Berlaku Hingga</label>
            <input type="date" name="valid_until" id="valid_until" class="form-control" required>
        </div>

        <h4 class="mt-4">Kriteria Diskon (Opsional)</h4>

        <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" id="category_id" class="form-control select2">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 d-none" id="produk-container">
            <label for="product_id" class="form-label">Produk</label>
            <select name="product_id" id="product_id" class="form-control select2">
                <option value="">Semua Produk</option>
            </select>
        </div>

        <div class="mb-3 d-none" id="min-purchase-container">
            <label for="min_qty" class="form-label">Minimal Jumlah Pembelian</label>
            <input type="number" name="min_qty" class="form-control" id="min_qty">
        </div>

        <div class="mb-3 d-none" id="min-price-container">
            <label for="min_price" class="form-label">Minimal Total Harga (Rp)</label>
            <input type="number" name="min_price" class="form-control" id="min_price">
        </div>

        <div class="mb-3">
            <label for="needed_poin" class="form-label">Poin Dibutuhkan</label>
            <input type="number" name="needed_poin" class="form-control" id="needed_poin">
        </div>

        <button type="submit" class="btn btn-primary">Tambah Diskon</button>
        <a href="/discount" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        // Sembunyikan input jumlah dan harga sebelum kategori/produk dipilih
        $('#category_id').change(function() {
            let categoryId = $(this).val();
            $('#produk-container').toggleClass('d-none', !categoryId);
            $('#min-purchase-container').toggleClass('d-none', !categoryId);
            $('#min-price-container').toggleClass('d-none', !categoryId);

            // Kosongkan produk saat kategori berubah
            $('#product_id').html('<option value="">Memuat Produk...</option>');

            if (categoryId) {
                $.ajax({
                    url: "{{ route('products.byCategory') }}",
                    data: { category_id: categoryId },
                    success: function(response) {
                        let options = '<option value="">Semua Produk</option>';
                        $.each(response, function(index, product) {
                            options += `<option value="${product.id}">${product.name}</option>`;
                        });
                        $('#product_id').html(options);
                    }
                });
            }
        });

        // Validasi diskon (hanya 1-100%)
        $('#discount').on('input', function() {
            let val = parseInt($(this).val());
            if (val < 1) $(this).val(1);
            if (val > 100) $(this).val(100);
        });
    });
</script>
@endsection
