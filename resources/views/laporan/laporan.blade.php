@extends('layouts.main')
@section('title', 'Laporan Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <!-- Header & Form Pencarian -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold">Laporan Penjualan</h4>
        </div>
        <!-- Form Filter dan Export Laporan -->
<form action="{{ route('laporan.export') }}" method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <label for="from_date" class="form-label">Dari Tanggal:</label>
            <input type="date" name="from_date" id="from_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="to_date" class="form-label">Sampai Tanggal:</label>
            <input type="date" name="to_date" id="to_date" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-file-excel"></i> Export Laporan
            </button>
        </div>
    </div>
</form>


    </div>

    <!-- Tabel Laporan Penjualan -->
    <div class="table-responsive">
        <table id="laporanTable" class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kasir</th>
                    <th>Tanggal & Waktu</th>
                    <th>Pelanggan</th>
                    <th>Tipe Pelanggan</th>
                    <th>Total Pembelanjaan</th>
                    <th>Diskon (Rp)</th>
                    <th>Poin Digunakan</th>
                    <th>Total Akhir Pembelanjaan</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->tgl }}</td>
                        <td>
                            {{ $item->member_id ? $item->member->nama : 'Non Membership' }}
                        </td>
                        <td>
                            @if($item->member_id)
                                @if($item->member->type == 1)
                                    Bronze
                                @elseif($item->member->type == 2)
                                    Silver
                                @elseif($item->member->type == 3)
                                    Gold
                                @else
                                    -
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>Rp. {{ number_format($item->total_harga, 2, ',', '.') }}</td>
                        <td>Rp. {{ number_format($item->diskon, 2, ',', '.') }}</td>
                        <td>{{ $item->used_poin ?? 0 }}</td>
                        <td>Rp. {{ number_format($item->total_harga - $item->diskon, 2, ',', '.') }}</td>
                        <td>
                            <a href="/detail={{ $item->kode_penjualan }}" class="btn btn-success btn-sm">
                                <i class="fa fa-search"></i> Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function(){
        $('#laporanTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { next: "Selanjutnya", previous: "Sebelumnya" }
            },
            order: [[2, "desc"]] // Mengurutkan berdasarkan Tanggal & Waktu transaksi terbaru
        });
    });
</script>
@endsection
