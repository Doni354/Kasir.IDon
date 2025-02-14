@extends('layouts.main')
@section('title', 'Transaksi')
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
                <form action="/pilih-produk" method="POST" @if($penjualan->status == 'paid') style="pointer-events: none; opacity: 0.6;" @endif>
                    @csrf
                    <input type="hidden" name="kode_penjualan" value="{{ $penjualan->kode_penjualan }}">
                    <div class="mb-3">
                        <label for="produk_id" class="form-label">Produk</label>
                        <select name="produk_id" id="produk-select" class="form-select">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} | Stok: {{ $item->totalStok() }} | Rp {{ number_format($item->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qty" class="form-label">Jumlah</label>
                        <input type="number" name="qty" class="form-control" id="qty" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Tambah ke Keranjang</button>
                </form>
            </div>
        </div>

        <div class="col-4">
            <div class="bg-white rounded h-100 p-4">
                <ul class="list-unstyled">
                    <li><strong>Invoice code:</strong> {{ $penjualan->kode_penjualan }}</li>
                    <li><strong>Time:</strong> {{ $penjualan->tgl }}</li>
                    <li><strong>Member:</strong> {{ $penjualan->member_id ? $penjualan->member->nama : 'Non Membership' }}</li>
                    <li><strong>Poin:</strong> {{ $penjualan->member_id ? $penjualan->member->poin : '-' }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Keranjang Belanja</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Harga Dasar</th>
                            <th>Markup</th>
                            <th>Harga Setelah Markup</th>
                            <th>PPN (12%)</th>
                            <th>Harga Akhir</th>
                            <th>Subtotal</th>
                            <th>*Set</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_harga = 0; @endphp
                        @foreach ($keranjang as $item)
                            @php
                                $hargaDasar = $item->produk->price;
                                $markup = ($item->harga_satuan / 1.12) - $hargaDasar;
                                $hargaSetelahMarkup = $hargaDasar + $markup;
                                $ppn = $hargaSetelahMarkup * 12 / 100;
                                $hargaAkhir = $hargaSetelahMarkup + $ppn;
                                $subtotal = $hargaAkhir * $item->qty;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->produk->name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>Rp. {{ number_format($hargaDasar) }}</td>
                                <td>Rp. {{ number_format($markup) }}</td>
                                <td>Rp. {{ number_format($hargaSetelahMarkup) }}</td>
                                <td>Rp. {{ number_format($ppn) }}</td>
                                <td>Rp. {{ number_format($hargaAkhir) }}</td>
                                <td>Rp. {{ number_format($subtotal) }}</td>
                                @php $total_harga += $subtotal; @endphp
                                <td>
                                    @if($penjualan->status != 'paid')
                                        <a class="btn btn-danger btn-sm" href="/hapus-produk={{ $item->id }}"><i class="fa fa-trash"></i> Hapus</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="8"></td>
                            <td><strong>Total Bayar:</strong></td>
                            <td><strong>Rp. {{ number_format($total_harga) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <form action="/bayar/{{ $penjualan->kode_penjualan }}" method="POST">
                @csrf
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Bayar</label>
                        <input type="hidden" name="total_harga" value="{{ $total_harga }}">
                        <input type="number" value="{{ session('bayar') ?? '' }}" name="bayar" class="form-control" @if($penjualan->status == 'paid') disabled @endif>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Kembalian</label>
                        <input type="number" value="{{ session('kembalian') ?? '' }}" name="kembalian" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary" type="submit" @if($penjualan->status == 'paid') disabled @endif>
                        <i class="fa fa-credit-card"></i> Bayar
                    </button>
                    @if(session('status'))
                        <a class="btn btn-success" href="/nota-penjualan/{{ $penjualan->kode_penjualan }}"><i class="fa fa-download"></i> Nota</a>
                        <a class="btn btn-info" onclick="return confirm('Apakah anda ingin meninggalkan halaman ini? Data transaksi ini sudah tersimpan')" href="/penjualan"><i class="fa fa-arrow-right"></i> Back</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
