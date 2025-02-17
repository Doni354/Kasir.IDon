@extends('layouts.main')

@section('title', 'Update Diskon')

@section('content')
<div class="container">
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update Diskon</h4>
        </div>
        <div class="card-body">
            @if(session('msg'))
                <div class="alert alert-success">
                    {{ session('msg') }}
                </div>
            @endif

            <form action="{{ route('discount.update', $discount->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nama Diskon -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Diskon</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama diskon" required value="{{ old('name', $discount->name) }}">
                </div>

                <!-- Diskon (%) -->
                <div class="mb-3">
                    <label for="discount" class="form-label">Diskon (%)</label>
                    <input type="number" name="discount" id="discount" class="form-control" placeholder="Masukkan diskon (1-100)" min="1" max="100" required value="{{ old('discount', $discount->discount) }}">
                </div>

                <!-- Berlaku Hingga -->
                <div class="mb-3">
                    <label for="valid_until" class="form-label">Berlaku Hingga</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control" required value="{{ old('valid_until', $discount->valid_until) }}">
                </div>

                <hr>

                <h5 class="mb-3">Kriteria Diskon</h5>

                <!-- Kategori -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-control select2" required>
                        <option value="" disabled>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $discount->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Produk -->
                <div class="mb-3" id="produk-container">
                    <label for="product_id" class="form-label">Produk</label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <!-- Saat load awal, tampilkan data produk yang sedang dipilih -->
                        <option value="{{ $discount->product_id }}" selected>
                            {{ $discount->product->name }}
                        </option>
                    </select>
                </div>

                <!-- Minimal Jumlah Pembelian -->
                <div class="mb-3" id="min-purchase-container">
                    <label for="min_qty" class="form-label">Minimal Jumlah Pembelian</label>
                    <input type="number" name="min_qty" id="min_qty" class="form-control" placeholder="Masukkan minimal pembelian" required value="{{ old('min_qty', $discount->min_qty) }}">
                </div>

                <!-- Poin Dibutuhkan -->
                <div class="mb-3">
                    <label for="needed_poin" class="form-label">Poin Dibutuhkan</label>
                    <input type="number" name="needed_poin" id="needed_poin" class="form-control" placeholder="Opsional, tidak perlu diisi" value="{{ old('needed_poin', $discount->needed_poin) }}">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Diskon</button>
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
        // Inisialisasi Select2 untuk semua dropdown
        $('.select2').select2({
            width: '100%'
        });

        // Simpan data produk yang sedang dipilih saat ini agar tidak hilang
        var currentProductId = "{{ $discount->product_id }}";
        var currentProductName = "{{ $discount->product->name }}";

        // Ketika kategori berubah, load produk berdasarkan kategori yang dipilih
        $('#category_id').change(function() {
            let categoryId = $(this).val();
            if (categoryId) {
                // Pastikan container produk dan minimal pembelian ditampilkan
                $('#produk-container, #min-purchase-container').removeClass('d-none');
                // Tampilkan pesan sementara saat memuat produk
                $('#product_id').html('<option value="">Memuat Produk...</option>');

                $.ajax({
                    url: "{{ route('products.byCategoryDiscount') }}",
                    data: { category_id: categoryId },
                    success: function(response) {
                        let options = '<option value="" disabled>Pilih Produk</option>';
                        var foundCurrent = false;
                        if (response.length > 0) {
                            $.each(response, function(index, product) {
                                // Jika produk saat ini ditemukan pada response, tandai sebagai selected
                                if(product.id == currentProductId) {
                                    foundCurrent = true;
                                    options += `<option value="${product.id}" selected>${product.name}</option>`;
                                } else {
                                    options += `<option value="${product.id}">${product.name}</option>`;
                                }
                            });
                        }
                        // Jika produk yang sedang dipilih tidak ada di response, tambahkan secara manual
                        if (!foundCurrent) {
                            options += `<option value="${currentProductId}" selected>${currentProductName}</option>`;
                        }
                        $('#product_id').html(options);
                    }
                });
            } else {
                // Jika tidak ada kategori yang dipilih, sembunyikan container produk dan minimal pembelian
                $('#produk-container, #min-purchase-container').addClass('d-none');
            }
        });

        // Validasi input diskon (agar nilai selalu antara 1 dan 100)
        $('#discount').on('input', function() {
            let val = parseInt($(this).val());
            if (val < 1) $(this).val(1);
            if (val > 100) $(this).val(100);
        });

        // Trigger event change pada kategori agar produk langsung termuat saat halaman di-load
        $('#category_id').trigger('change');
    });
</script>
@endsection
