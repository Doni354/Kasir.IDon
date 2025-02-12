@php
            use Carbon\Carbon;
            $today = Carbon::today(); // Tanggal hari ini tanpa jam
        @endphp
@extends('layouts.main')
@section('title', 'Stok')
@section('content')
<div class="container">
    <div class="row g-4">
        <h6 class="mb-4">Data Stok</h6>
        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(auth()->user()->role == 'admin')
            <div class="d-flex justify-content-end mb-3">
                <a href="/stok-tambah" class="btn btn-primary"><i class="fa fa-plus"></i> tambah Stok</a>
            </div>
            @endif

            <table id="stokTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Stok</th>
                        <th>Expired Date</th>
                        <th>Buy Date</th>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <th scope="col">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokList as $index => $stok)
                        @php
                            $expiredDate = Carbon::parse($stok->expired_date);
                            $diffDays = $today->diffInDays($expiredDate, false);

                            if ($diffDays < 0) {
                                $statusText = "Barang Expired";
                                $rowClass = "bg-danger text-white";
                            } elseif ($diffDays == 0) {
                                $statusText = "Expired Hari Ini";
                                $rowClass = "bg-danger text-white";
                            } elseif ($diffDays <= 5) {
                                $statusText = "Expired dalam $diffDays hari";
                                $rowClass = "bg-warning text-white";
                            } else {
                                $statusText = "";
                                $rowClass = "text-dark";
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stok->product->name ?? 'Tidak Ada' }}</td>
                            <td>{{ $stok->qty }}</td>
                            <td>
                                {{ $stok->expired_date ? date('d-m-Y', strtotime($stok->expired_date)) : '-' }}
                                @if ($statusText)
                                    <span>({{ $statusText }})</span>
                                @endif
                            </td>
                            <td>{{ $stok->buy_date ? date('d-m-Y', strtotime($stok->buy_date)) : '-' }}</td>
                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                                <td>
                                    <a href="{{ url('/stok-edit='.$stok->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $stok->id }}">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>



        @foreach ($stokList as $index => $stok)
 <!-- Modal Konfirmasi Hapus -->
 <div class="modal fade" id="deleteModal{{ $stok->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $stok->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $stok->id }}">Konfirmasi Hapus</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus stok produk <strong>{{ $stok->product->name }}</strong>?<br>
                Tekan tombol "Delete" untuk menghapus.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>

                <form action="{{ route('stok.destroy', $stok->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
</div>
@endsection
@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

            <script>

                $(document).ready(function() {
                    $('#stokTable').DataTable({
                        "language": {
                            "search": "Cari:",
                            "lengthMenu": "Tampilkan _MENU_ entri",
                            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            "paginate": {
                                "first": "Pertama",
                                "last": "Terakhir",
                                "next": "Selanjutnya",
                                "previous": "Sebelumnya"
                            }
                        }
                    });
                });
            </script>
@endsection
