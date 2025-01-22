@extends('layouts.main')
@section( 'title', 'Transaksi')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4 mb-4">
        <div class="col-8">
            <div class="bg-light rounded h-100 p-4">
                @if($errors->any())
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <h6 class="mb-4">Pilih Produk</h6>
                <form action="/pilih-produk" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="y" class="form-label">Produk</label>
                        <input type="hidden" name="kode_penjualan" value="{{ $penjualan->kode_penjualan }}">
                        <select name="produk_id" class="form-select">
                            @foreach ($produk as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }} | {{ $item->stok }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="y" class="form-label">Jumlah</label>
                        <input type="number" name="qty" class="form-control" id="y">
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>

        <div class="col-4">
            <div class="bg-white rounded h-100 p-4">
                <ul class="list-unstyled">
                    <li><strong>Invoice code:</strong>{{ $penjualan->kode_penjualan }}</li>
                    <li><strong>Time:</strong>{{ $penjualan->tgl }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Keranjang Belanja</h6>
            <div class="table-responsive">
                @if(session('msg'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('msg') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">*Set</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_harga =  0;
                        @endphp
                        @foreach ($keranjang as $item)
                            <tr>
                                <td >{{ $loop->iteration }}</td>
                                <td>{{ $item->produk->nama }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>Rp. {{ number_format($item->produk->harga) }}</td>
                                <td>Rp. {{ number_format($item->subtotal) }}</td>
                                @php
                                    $total_harga += $item->subtotal;
                                @endphp
                                <td>
                                    <a class="btn btn-danger btn-sm" href="/hapus-produk={{ $item->id }}"><i class="fa fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="2">Total Harga: {{ $total_harga }}</td>
                        </tr>
                    </tbody>
                </table>
            </div><hr>
            {{-- form bayar --}}
            <form action="/bayar/{{ $penjualan->kode_penjualan }}" method="POST">
                @csrf
                <div class="col-6">
                    <div class="mb-3">
                        <label for="y" class="form-label">Bayar</label>
                        <input type="hidden" name="total_harga" value="{{ $total_harga }}">
                        <input type="number" value="{{ session('bayar') ?? "" }}" name="bayar" class="form-control" id="y">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label for="y" class="form-label">Kembalian</label>
                        <input type="number" value="{{ session('kembalian') ?? "" }}"  name="kembalian" class="form-control" id="y" disabled>
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Bayar</button>
                    @if(session('status'))
                        <a class="btn btn-success" href="/nota-penjualan/{{ $penjualan->kode_penjualan }}"><i class="fa fa-download"></i> Nota</a>
                        <a class="btn btn-info" onclick="return confirm('Apakah anda ingn meninggalkan halaman ini? Data Transaksi ini sudah Tersimpan')" href="/penjualan"><i class="fa fa-arrow-right"></i> Back</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
