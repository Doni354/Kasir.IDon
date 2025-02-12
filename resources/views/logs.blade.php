@extends('layouts.main')
@section('title', 'Logs')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Logs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->user->name ?? 'Unknown' }}</td>
                            <td>
                                @php
                                    // Tentukan warna badge berdasarkan action
                                    $badgeClass = match ($log->action) {
                                        'CREATE' => 'success',
                                        'UPDATE' => 'warning',
                                        'DELETE' => 'danger',
                                        'login', 'logout', 'register' => 'primary',
                                        'FAILED_LOGIN' => 'dark',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ strtoupper($log->action) }}</span>
                            </td>
                            <td>{{ ucfirst($log->table_name) }}</td>
                            <td>{{ $log->msg }}</td>
                            <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm view-log"
                                    data-user="{{ $log->user->name ?? 'Unknown' }}"
                                    data-action="{{ strtoupper($log->action) }}"
                                    data-model="{{ ucfirst($log->table_name) }}"
                                    data-msg="{{ $log->msg }}"
                                    data-record-id="{{ $log->record_id }}"
                                    data-ip="{{ $log->ip_address }}"
                                    data-user-agent="{{ $log->user_agent }}"
                                    data-old='@json(json_decode($log->old_data, true))'
                                    data-new='@json(json_decode($log->new_data, true))'
                                    data-created="{{ $log->created_at->format('d M Y H:i:s') }}">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal Detail Log -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="logDetailModalLabel">
                    <i class="fas fa-history"></i> Detail Log
                </h5>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <!-- Informasi Pengguna -->
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user"></i> Informasi Pengguna</h6>
                            <p><strong>User:</strong> <span id="logUser"></span></p>
                            <p><strong>IP Address:</strong> <span id="logIp"></span></p>
                            <p><strong>User Agent:</strong> <span id="logUserAgent"></span></p>
                        </div>
                        <!-- Informasi Log -->
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-database"></i> Informasi Log</h6>
                            <p><strong>Aksi:</strong> <span id="logAction"></span></p>
                            <p><strong>Model:</strong> <span id="logModel"></span></p>
                            <p><strong>ID Record:</strong> <span id="logRecordId"></span></p>
                            <p><strong>Dibuat Pada:</strong> <span id="logCreated"></span></p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-primary"><i class="fas fa-comment"></i> Pesan Log</h6>
                    <div class="alert alert-info" role="alert">
                        <span id="logMsg"></span>
                    </div>

                    <hr>

                    <div class="row">
                        <!-- Data Lama -->
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="fas fa-arrow-left"></i> Data Lama</h6>
                            <pre class="bg-light p-3 rounded border" id="logOldData"></pre>
                        </div>
                        <!-- Data Baru -->
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-arrow-right"></i> Data Baru</h6>
                            <pre class="bg-light p-3 rounded border" id="logNewData"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables CSS dan JS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi DataTables pada tabel dengan id "dataTable"
        $('#dataTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            }
        });

        // Script untuk menampilkan detail log pada modal
        $('.view-log').click(function () {
            $('#logUser').text($(this).data('user'));
            $('#logAction').text($(this).data('action'));
            $('#logModel').text($(this).data('model'));
            $('#logMsg').text($(this).data('msg'));
            $('#logRecordId').text($(this).data('record-id'));
            $('#logIp').text($(this).data('ip'));
            $('#logUserAgent').text($(this).data('user-agent'));
            $('#logCreated').text($(this).data('created'));

            let oldData = $(this).data('old');
            let newData = $(this).data('new');

            $('#logOldData').text(oldData ? JSON.stringify(oldData, null, 4) : 'Tidak ada data lama');
            $('#logNewData').text(newData ? JSON.stringify(newData, null, 4) : 'Tidak ada data baru');

            $('#logDetailModal').modal('show');
        });
    });
</script>
@endsection
