@extends('layouts.main')
@section('title', 'Member')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h6 class="mb-4">Data Member</h6>

        <div class="table-responsive">
            @if(session('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('msg') }}
                </div>
            @endif

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Member</h5>
                <a href="{{ route('member.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Member</a>
            </div>

            <table class="table table-bordered table-striped" id="dataTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Poin</th>
                        <th>Type</th>
                        <th>Option</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($members as $key => $member)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $member->nama }}</td>
                        <td>{{ number_format($member->poin) }}</td>
                        <td>
                            @php
                                $typeLabels = ['1' => 'Bronze', '2' => 'Silver', '3' => 'Gold'];
                            @endphp
                            <span class="badge
                                {{ $member->type == 1 ? 'bg-warning' : ($member->type == 2 ? 'bg-secondary' : 'bg-success') }}">
                                {{ $typeLabels[$member->type] ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $member->id }}">
                                <i class="fa fa-eye"></i> Detail
                            </button>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('member.destroy', $member->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus member ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Detail Member -->
                    <div class="modal fade" id="detailModal{{ $member->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $member->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="detailModalLabel{{ $member->id }}">Detail Member</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <strong>Nama:</strong> <span class="text-dark">{{ $member->nama }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Email:</strong> <span class="text-dark">{{ $member->email }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Phone:</strong> <span class="text-dark">{{ $member->phone }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Poin:</strong> <span class="text-dark">{{ number_format($member->poin) }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Type:</strong>
                                        <span class="badge
                                            {{ $member->type == 1 ? 'bg-warning' : ($member->type == 2 ? 'bg-secondary' : 'bg-success') }}">
                                            {{ $typeLabels[$member->type] ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Dibuat:</strong> <span class="text-dark">{{ $member->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Update Terakhir:</strong> <span class="text-dark">{{ $member->updated_at->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
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
