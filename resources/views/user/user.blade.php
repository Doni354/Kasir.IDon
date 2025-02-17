@extends('layouts.main')
@section( 'title', 'User')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data User</h6>
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
            <div class="d-flex justify-content-end mb-3">
                <a href="/tambah-user" class="btn btn-primary"><i class="fa fa-plus"></i> User Baru</a>
            </div>
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Option</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($user as $index => $item)
                    @if ($item->role !== 'admin') {{-- Sembunyikan admin --}}
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->role }}</td>
                            <td>
                                <a class="btn btn-success btn-sm" href="/edit-user={{ $item->id }}"><i class="fa fa-edit"></i> Edit</a>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#DeleteModal">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>

                            </td>
                        </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Logout Modal-->
    <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Are you sure want to Delete this items? <br> Press the "Delete" Button to proceed</div>
                <div class="modal-footer">

                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        @csrf
                        <a class="btn btn-danger btn-sm" href="/hapus-user/{{ $item->id }}"><i class="fa fa-trash"></i> Delete</a>

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

