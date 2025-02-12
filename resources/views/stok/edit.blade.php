@extends('layouts.main')
@section('title', 'Edit Stok')
@section('content')
<div class="container">
    <h2>Edit Stok</h2>
    <form action="{{ route('stok.update', $stok->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="product_id" class="form-label">Produk</label>
            <select name="product_id" id="product_id" class="form-control select2">
                <option value="{{ $stok->product_id }}" selected>{{ $stok->product->name ?? 'Tidak Ada' }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="qty" class="form-label">Jumlah Stok</label>
            <input type="number" name="qty" id="qty" class="form-control" value="{{ $stok->qty }}" required>
        </div>

        <div class="mb-3">
            <label for="expired_date" class="form-label">Tanggal Kadaluarsa</label>
            <input type="date" name="expired_date" id="expired_date" class="form-control"
                   value="{{ $stok->expired_date ? date('Y-m-d', strtotime($stok->expired_date)) : '' }}" required>
        </div>

        <div class="mb-3">
            <label for="buy_date" class="form-label">Tanggal Pembelian</label>
            <input type="date" name="buy_date" id="buy_date" class="form-control"
                   value="{{ $stok->buy_date ? date('Y-m-d', strtotime($stok->buy_date)) : '' }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>

</div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#product_id').select2({
                placeholder: "Cari dan Pilih Produk",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('products.search') }}",
                    dataType: 'json',
                    delay: 250,
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
