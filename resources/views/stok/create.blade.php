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

        <!-- Pilihan Produk -->
        <div class="mb-3">
            <label for="product_id" class="form-label">Pilih Produk</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date().toISOString().split('T')[0];

        document.getElementById("expired_date").setAttribute("min", today);
        document.getElementById("buy_date").setAttribute("min", today);
    });
</script>

@endsection
