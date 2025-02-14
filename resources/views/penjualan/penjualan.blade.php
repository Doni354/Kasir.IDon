@extends('layouts.main')
@section( 'title', 'Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data Penjualan</h6>
        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
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

            <!-- Tombol Pilih Pelanggan -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
        <i class="fa fa-plus"></i> Pilih Pelanggan
    </button>
</div>

<!-- Modal Pilih Member -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/pilih-member" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="member_id" class="col-form-label">Member</label>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <select name="member_id" id="member-select" class="form-select">
                            <option value="">-- Pilih Member (Opsional) --</option>
                            @foreach ($member as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nama }} - {{ $item->phone }} - {{ $item->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Pilih</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan jQuery dan Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#member-select').select2({
            width: '100%',
            placeholder: "Cari Member...",
            allowClear: true
        });
    });
</script>


            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Total Bayar</th>
                        <th scope="col">Kasir</th>
                        <th scope="col">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $item)
                        <tr>
                            <td >{{ $loop->iteration }}</td>
                            <td>{{ $item->tgl }}</td>
                            <td>{{ $item->kode_penjualan }}</td>
                            <td>Rp. {{ number_format($item->total_harga) }}</td>
                            <td>Rp. {{ number_format($item->bayar) }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>
                                <a class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus penjualan ini?')" href="/hapus-penjualan/{{ $item->kode_penjualan }}"><i class="fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
