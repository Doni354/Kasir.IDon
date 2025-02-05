@extends('layouts.main')
@section( 'title', 'Logs')

@section('content')
<div class="container">
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Logs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Message</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                   
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user->name ?? 'Unknown' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->model }}</td>
                            <td>{{ $log->msg }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection

