@extends('layouts.main')
@section('title', 'Transaksi')
@section('content')
<div class="container-fluid pt-4 px-4">
    <!-- Form Pilih Produk & Info Penjualan -->
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
    @if(session('msg'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Info: </strong> {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <!-- Tabel Keranjang Belanja (Detail) -->
    <div class="row g-4">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Keranjang Belanja</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary">
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
                            <th>*Aksi</th>
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
                                $originalAmount = $hargaAkhir * $item->qty;
                                $subtotal = $item->discount_applied ? $item->subtotal : $originalAmount;
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
                                <td>
                                    @if($item->discount_applied)
                                        <del>Rp. {{ number_format($originalAmount) }}</del>
                                        <br>
                                        <span class="text-success">Rp. {{ number_format($item->subtotal) }}</span>
                                        <br>
                                        @php
                                            $appliedDiscount = \App\Models\Discount::find($item->discount_id);
                                        @endphp
                                        <small>
                                            Diskon: {{ $appliedDiscount->discount }}% (Potongan: Rp. {{ number_format($item->discount_amount) }})
                                            @if($appliedDiscount->needed_poin > 0)
                                                <br>Poin dikurangi: {{ $appliedDiscount->needed_poin }} poin
                                            @endif
                                        </small>
                                    @else
                                        Rp. {{ number_format($subtotal) }}
                                    @endif
                                </td>
                                @php $total_harga += $subtotal; @endphp
                                <td>
                                    @if($penjualan->status != 'paid')
                                        <a class="btn btn-danger btn-sm" href="/hapus-produk={{ $item->id }}">
                                            <i class="fa fa-trash"></i> Hapus
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="8" class="text-end"><strong>Total Bayar:</strong></td>
                            <td colspan="2"><strong>Rp. {{ number_format($total_harga) }}</strong></td>
                        </tr>
                    </tbody>

                </table>
            </div>
            <hr>
            <!-- Form Pembayaran -->
            <form action="/bayar/{{ $penjualan->kode_penjualan }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bayar</label>
                        <input type="hidden" name="total_harga" value="{{ $total_harga }}">
                        <input type="number" name="bayar" class="form-control" @if($penjualan->status == 'paid') disabled @endif>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kembalian</label>
                        <input type="number" name="kembalian" class="form-control" disabled>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit" @if($penjualan->status == 'paid') disabled @endif>
                            <i class="fa fa-credit-card"></i> Bayar
                        </button>
                        @if(session('status'))
                            <a class="btn btn-success" href="/nota-penjualan/{{ $penjualan->kode_penjualan }}">
                                <i class="fa fa-download"></i> Nota
                            </a>
                            <a class="btn btn-info" onclick="return confirm('Apakah anda ingin meninggalkan halaman ini? Data transaksi sudah tersimpan')" href="/penjualan">
                                <i class="fa fa-arrow-right"></i> Back
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Tampilan Informasi Poin Member Setelah Pembayaran -->
            @if(session('poin_tambahan'))
            <div class="alert alert-info mt-3">
                <strong>Poin Tambahan:</strong> {{ session('poin_tambahan') }} <br>
                <strong>Total Poin Baru:</strong> {{ session('poin_total') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Tabel Diskon Tersedia -->
    <div class="row g-4">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Daftar Diskon Tersedia</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Diskon (%)</th>
                            <th>Potongan (Rp)</th>
                            <th>Poin (Jika Ada)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($keranjang as $item)
                            @php
                                $availableDiscount = \App\Models\Discount::where('product_id', $item->produk_id)
                                    ->where('min_qty', '<=', $item->qty)
                                    ->where('status', 1)
                                    ->whereDate('valid_until', '>=', now())
                                    ->first();
                                $originalAmount = $item->harga_satuan * $item->qty;
                                $potongan = $availableDiscount ? ($availableDiscount->discount / 100) * $originalAmount : 0;
                            @endphp
                            @if($availableDiscount)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->produk->name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ $availableDiscount->discount }}%</td>
                                    <td>Rp. {{ number_format($potongan) }}</td>
                                    <td>
                                        @if($availableDiscount->needed_poin > 0)
                                            {{ $availableDiscount->needed_poin }} poin
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->discount_applied && $item->discount_id == $availableDiscount->id)
                                            <a href="{{ route('cancel.discount', $item->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-times"></i> Batalkan Diskon
                                            </a>
                                        @else
                                            <a href="{{ route('apply.discount', ['detail_id' => $item->id, 'discount_id' => $availableDiscount->id]) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-check"></i> Gunakan Diskon
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
