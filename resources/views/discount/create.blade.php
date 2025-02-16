@extends('layouts.main')

@section('title', 'Tambah Diskon')

@section('content')
<div class="container">
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tambah Diskon</h4>
        </div>
        <div class="card-body">
            @if(session('msg'))
                <div class="alert alert-success">
                    {{ session('msg') }}
                </div>
            @endif

            <form action="{{ route('discount.store') }}" method="POST">
                @csrf

                <!-- Nama Diskon (wajib diisi) -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Diskon</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama diskon" required>
                </div>

                <!-- Diskon (%) (wajib diisi, nilai antara 1-100) -->
                <div class="mb-3">
                    <label for="discount" class="form-label">Diskon (%)</label>
                    <input type="number" name="discount" class="form-control" id="discount" placeholder="Masukkan diskon (1-100)" min="1" max="100" required>
                </div>

                <!-- Berlaku Hingga (wajib diisi) -->
                <div class="mb-3">
                    <label for="valid_until" class="form-label">Berlaku Hingga</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control" required>
                </div>

                <hr>

                <h5 class="mb-3">Kriteria Diskon</h5>

                <!-- Kategori (wajib diisi) -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-control select2" required>
                        <option value="" selected disabled>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Produk (wajib diisi ketika kategori dipilih) -->
                <div class="mb-3 d-none" id="produk-container">
                    <label for="product_id" class="form-label">Produk</label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <option value="" selected disabled>Semua Produk</option>
                    </select>
                </div>

                <!-- Minimal Jumlah Pembelian (wajib diisi ketika kategori dipilih) -->
                <div class="mb-3 d-none" id="min-purchase-container">
                    <label for="min_qty" class="form-label">Minimal Jumlah Pembelian</label>
                    <input type="number" name="min_qty" class="form-control" id="min_qty" placeholder="Masukkan minimal pembelian" required>
                </div>

                <!-- Poin Dibutuhkan (opsional) -->
                <div class="mb-3">
                    <label for="needed_poin" class="form-label">Poin Dibutuhkan</label>
                    <input type="number" name="needed_poin" class="form-control" id="needed_poin" placeholder="Opsional, tidak perlu diisi">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Tambah Diskon</button>
                    <a href="/discount" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Library Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi select2 untuk dropdown
        $('.select2').select2({
            width: '100%'
        });

        // Tampilkan input Produk dan Minimal Pembelian saat kategori dipilih
        $('#category_id').change(function() {
    let categoryId = $(this).val();
    if (categoryId) {
        $('#produk-container, #min-purchase-container').removeClass('d-none');
        $('#product_id').html('<option value="">Memuat Produk...</option>');

        $.ajax({
            url: "{{ route('products.byCategoryDiscount') }}",
            data: { category_id: categoryId },
            success: function(response) {
                let options = '<option value="" selected disabled>Pilih Produk</option>';
                if (response.length > 0) {
                    $.each(response, function(index, product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                } else {
                    options += '<option value="" disabled>Tidak ada produk tersedia</option>';
                }
                $('#product_id').html(options);
            }
        });
    } else {
        $('#produk-container, #min-purchase-container').addClass('d-none');
    }
});

        // Validasi input diskon agar nilainya berada di antara 1 sampai 100%
        $('#discount').on('input', function() {
            let val = parseInt($(this).val());
            if (val < 1) $(this).val(1);
            if (val > 100) $(this).val(100);
        });
    });
</script>
@endsection
