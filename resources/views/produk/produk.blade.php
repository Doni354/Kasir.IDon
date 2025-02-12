@extends('layouts.main')

@section('title', 'Produk')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data Produk</h6>
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
                <a href="/produk-tambah" class="btn btn-primary"><i class="fa fa-plus"></i> Produk Baru</a>
            </div>
            @endif

            <table id="produkTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <th scope="col">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->name }}</td>
                            <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                            <td>{{ $p->kategori->name ?? '-' }}</td>
                            <td>
                                @php
                                    $totalStok = $p->totalStok(); // Hitung hanya stok yang belum expired
                                @endphp
                                @if($totalStok <= 5 && $totalStok > 0)
                                    {{ $totalStok }}
                                    <span class="text-sm text-danger"> *</span><i> Stok Menipis</i>
                                @elseif($totalStok == 0)
                                    <span class="text-sm text-danger"> *</span><i> Stok Habis</i>
                                @else
                                    {{ $totalStok }}
                                @endif
                            </td>
                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <td>
                                <a href="{{ url('/edit-produk='.$p->id) }}" class="btn btn-warning">Edit</a>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $p->id }}">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach($produk as $p)
        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="deleteModal{{ $p->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $p->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $p->id }}">Konfirmasi Hapus</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus produk <strong>{{ $p->name }}</strong>?<br>
                        Tekan tombol "Delete" untuk menghapus.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>

                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST">
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
</div>
@endsection

@section('scripts')
<!-- Tambahkan DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#produkTable').DataTable();
    });
</script>
@endsection
