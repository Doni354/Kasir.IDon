@extends('layouts.main')
@section('title', 'Penjualan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">



        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                </div>
            @endif

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Data Penjualan</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalMember">
                    <i class="fa fa-user"></i> Tambah Transaksi
                </button>
            </div>
        @if(session('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('msg') }}
               
            </div>
        @endif




        <!-- Modal Pilih Member -->
        <div class="modal fade" id="modalMember" tabindex="-1" aria-labelledby="modalMemberLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMemberLabel">Pilih Member (Opsional)</h5>

                    </div>
                    <div class="modal-body">
                        <form action="/pilih-member" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <div class="mb-3">
                                <label for="member-select" class="form-label">Member</label>
                                <select name="member_id" id="member-select" class="form-select">
                                    <option value="">-- Non-Member --</option>
                                    @foreach ($member as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->nama }} - {{ $item->phone }} - {{ $item->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer pt-0">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm">Pilih</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Penjualan -->
        <div class="table-responsive">
            <table id="penjualanTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Total Harga</th>
                        <th>Total Bayar</th>
                        <th>Kasir</th>
                        <th style="width:120px;">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->tgl }}</td>
                            <td>{{ $item->kode_penjualan }}</td>
                            <td>Rp. {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($item->bayar, 0, ',', '.') }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>
                                <a href="/proses-penjualan={{ $item->kode_penjualan }}" class="btn btn-info btn-sm me-1">
                                    <i class="fa fa-eye"></i> Detail
                                </a>
                                <a onclick="return confirm('Apakah anda yakin ingin menghapus penjualan ini?')"
                                   href="/hapus-penjualan/{{ $item->kode_penjualan }}"
                                   class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#penjualanTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { next: "Selanjutnya", previous: "Sebelumnya" }
            },
            order: [[1, "desc"]] // Urutkan berdasarkan Tanggal desc
        });

        // Inisialisasi Select2
        $('#member-select').select2({
            width: '100%',
            placeholder: "Cari nama/HP/email member...",
            allowClear: true
        });
    });
</script>
@endsection
