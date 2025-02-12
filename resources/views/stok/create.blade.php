@extends('layouts.main')

@section('title', 'Tambah Stok')

@section('content')
<div class="container pt-4">
    <h4 class="mb-4">Tambah Stok</h4>

    @if(session('msg'))
        <div class="alert alert-success">{{ session('msg') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stok.store') }}" method="POST">
        @csrf

        <!-- Pilihan Produk dengan Search -->
        <div class="mb-3">
            <label for="product_id" class="form-label">Pilih Produk</label>
            <select name="product_id" id="product_id" class="form-control select2" required>
                <option value="" selected disabled>Memuat data produk...</option>
            </select>
        </div>

        <!-- Input Qty -->
        <div class="mb-3">
            <label for="qty" class="form-label">Jumlah Stok</label>
            <input type="number" name="qty" id="qty" class="form-control" min="1" required>
        </div>

        <!-- Input Expired Date -->
        <div class="mb-3">
            <label for="expired_date" class="form-label">Tanggal Kedaluwarsa</label>
            <input type="date" name="expired_date" id="expired_date" class="form-control" required>
        </div>

        <!-- Input Buy Date -->
        <div class="mb-3">
            <label for="buy_date" class="form-label">Tanggal Pembelian</label>
            <input type="date" name="buy_date" id="buy_date" class="form-control" required>
        </div>

        <!-- Tombol Simpan -->
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="/stok" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection

@section('scripts')
    <!-- Tambahkan Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Select2 untuk pencarian produk
            $('#product_id').select2({
                placeholder: "Cari dan Pilih Produk",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('products.search') }}", // Endpoint pencarian produk
                    dataType: 'json',
                    delay: 250, // Mengurangi beban server
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

            // Batasi tanggal agar tidak bisa memilih sebelum hari ini
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("expired_date").setAttribute("min", today);
            document.getElementById("buy_date").setAttribute("min", today);
        });
    </script>
@endsection
