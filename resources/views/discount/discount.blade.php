@php
    use Carbon\Carbon;
    $today = Carbon::today();
@endphp

@extends('layouts.main')
@section('title', 'Discount')
@section('content')

<div class="container mt-4">
    <h4 class="mb-4 text-primary">Daftar Diskon</h4>

    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
        <div class="d-flex justify-content-end mb-3">
            <a href="/discount-tambah" class="btn btn-primary"><i class="fa fa-plus"></i> Buat Discount</a>
        </div>
    @endif

    <div class="table-responsive">
        <table id="discountTable" class="table table-striped table-bordered">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Diskon</th>
                    <th>Status</th>
                    <th>Berlaku Hingga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discounts as $index => $discount)
                    @php
                        $validStatus = '';
                        $validClass = '';
                        $validUntil = Carbon::parse($discount->valid_until)->endOfDay(); // Pastikan hari berakhir di 23:59:59
                        $diffDays = $validUntil->diffInDays($today);

                        if ($validUntil->isPast()) {
                            $validStatus = 'Diskon Sudah Berakhir';
                            $validClass = 'text-danger fw-bold';
                        } elseif ($validUntil->isToday()) {
                            $validStatus = 'Terakhir Hari Ini';
                            $validClass = 'text-warning fw-bold';
                        } elseif ($diffDays <= 3) {
                            $validStatus = 'Segera Berakhir (' . $diffDays . ' hari lagi)';
                            $validClass = 'text-info fw-bold';
                        } else {
                            $validStatus = 'Masih Berlaku';
                            $validClass = 'text-success fw-bold';
                        }
                    @endphp

                    <tr class="text-center">
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $discount->name }}</td>
                        <td class="fw-bold">{{ $discount->discount }}%</td>
                        <td><span class="{{ $validClass }}">{{ $validStatus }}</span></td>
                        <td>{{ $validUntil->format('d M Y') }}</td>
                        <td>

                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $discount->id }}">
                                <i class="fa fa-eye"></i> Detail
                            </button>
                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'petugas')
                            <a href="{{ route('discount.edit', $discount->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('discount.destroy', $discount->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form>
                        @endif

                        </td>
                    </tr>

                    <!-- Modal Detail Diskon -->
                    <div class="modal fade" id="detailModal{{ $discount->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $discount->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="detailModalLabel{{ $discount->id }}">Detail Diskon</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Nama:</strong> {{ $discount->name }}</li>
                                        <li class="list-group-item"><strong>Diskon:</strong> {{ $discount->discount }}%</li>
                                        <li class="list-group-item"><strong>Poin Dibutuhkan:</strong> {{ $discount->needed_poin ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Minimal Kuantitas:</strong> {{ $discount->min_qty ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Minimal Harga:</strong> Rp {{ number_format($discount->min_price, 2) }}</li>
                                        <li class="list-group-item"><strong>Produk:</strong> {{ $discount->product->name ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Kategori:</strong> {{ $discount->category->name ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Berlaku Sampai:</strong> {{ $validUntil->format('d M Y') }}</li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Akhir Modal -->
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#discountTable').DataTable({
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
