@extends('layouts.main')
@section('title', 'Home')
@section('content')
<div class="container-fluid pt-4 px-4">


    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            @php
              // Tentukan rentang tanggal bulan ini
              $start = date('Y-m-01');
              $end = date('Y-m-t');

              // Ambil data penjualan pada bulan ini
              $monthlyData = \App\Models\Penjualan::whereBetween('tgl', [$start, $end])->get();

              // Jumlahkan total markup dan total diskon dari seluruh transaksi bulan ini
              $totalMarkup = $monthlyData->sum('markup');
              $totalDiskon = $monthlyData->sum('diskon');

              // Keuntungan = Total Markup - Total Diskon
              $keuntungan = $totalMarkup - $totalDiskon;
            @endphp
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Earnings (Monthly)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp. {{ number_format($keuntungan, 0, ',', '.') }}
                            </div>

                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6 mb-4">
            @php
              // Mengambil total PPN dari seluruh transaksi.
              // Jika ingin periode tertentu, tambahkan whereBetween atau filter sesuai kebutuhan.
              $totalPPN = \App\Models\Penjualan::sum('ppn');
            @endphp
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total PPN (12%)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp. {{ number_format($totalPPN, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6 mb-4">
            @php
                 $totalHargaProduk = \App\Models\Produk::all()->sum(function($p) {
                     return $p->price * $p->totalStok();
                 });
            @endphp
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Harga Produk (Stok)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp. {{ number_format($totalHargaProduk, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6 mb-4">
            @php
                // Menghitung total harga produk yang sudah expired.
                // Pastikan untuk mengganti \App\Models\Stok sesuai namespace yang benar.
                $totalExpiredPrice = \App\Models\Produk::all()->sum(function($p) {
                    return $p->price * $p->hasMany(\App\Models\Stok::class, 'product_id', 'id')
                        ->where('expired_date', '<=', now())
                        ->where('status', 1)
                        ->sum('qty');
                });
            @endphp
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Harga Produk Expired
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                - Rp. {{ number_format($totalExpiredPrice, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
  <!-- Grafik Transaksi Harian -->
  <div class="row mb-5">
    <div class="col-xl-8 col-lg-10 mx-auto">
      <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 fw-bold text-primary">Grafik Transaksi Harian</h6>
        </div>
        <div class="card-body">
          <div class="chart-area">
            <canvas id="transaksiChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Transaksi Harian -->
  <div class="row mb-5">
    <div class="col-xl-8 col-lg-10 mx-auto">
      <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 fw-bold text-primary">Grafik Stok Produk</h6>
        </div>
        <div class="card-body">
          <div class="chart-area">
            <canvas id="chartSemuaProduk" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>



  <!-- Data Barang Re-Order: Grafik & Tabel -->
  <div class="row">
    <div class="col-xl-8 col-lg-10 mx-auto">
      <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 fw-bold text-primary">Data Barang Re-Order</h6>
        </div>
        <div class="card-body">

          <!-- Tabel Re-Order -->
          <div class="table-responsive">
            <table id="reorderTable" class="table table-striped table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Nama Produk</th>
                  <th>Harga</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                </tr>
              </thead>
              <tbody>
                @foreach($reorderProducts as $index => $product)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>Rp. {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td>{{ $product->kategori->name ?? '-' }}</td>
                    <td>{{ $product->totalStok() }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div> <!-- End Card Body -->
      </div> <!-- End Card -->
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables CSS & JS for Tabel Re-Order -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function(){
    // Grafik Transaksi Harian
    const ctxTrans = document.getElementById("transaksiChart").getContext("2d");
    const transaksiChart = new Chart(ctxTrans, {
      type: 'line',
      data: {
        labels: {!! json_encode($transLabels) !!},
        datasets: [{
          label: "Total Transaksi (Rp)",
          data: {!! json_encode($transData) !!},
          fill: false,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          tension: 0.3,
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
        }]
      },
      options: {
        scales: {
          x: {
            title: { display: true, text: 'Tanggal' },
            grid: { display: false },
            ticks: { maxTicksLimit: 7 }
          },
          y: {
            title: { display: true, text: 'Total Transaksi (Rp)' },
            ticks: {
              callback: function(value) { return 'Rp. ' + value.toLocaleString(); }
            },
            grid: {
              color: "rgba(234, 236, 244, 1)",
              zeroLineColor: "rgba(234, 236, 244, 1)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) label += ': ';
                if (context.parsed.y !== null) {
                  label += 'Rp. ' + context.parsed.y.toLocaleString();
                }
                return label;
              }
            }
          }
        }
      }
    });

// Konversi data PHP ke format JSON agar dapat digunakan di JS
    const labels = {!! json_encode($produkLabels) !!};
    const dataStok = {!! json_encode($produkData) !!};

    const ctx = document.getElementById('chartSemuaProduk').getContext('2d');
    const chartSemuaProduk = new Chart(ctx, {
        type: 'bar', // tipe grafik, misalnya bar chart
        data: {
            labels: labels,
            datasets: [{
                label: 'Stok Produk',
                data: dataStok,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Menghindari tampilan angka pecahan
                        precision: 0
                    }
                }
            }
        }
    });

    // Inisialisasi DataTables untuk tabel Re-Order
    $('#reorderTable').DataTable({
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: { next: "Selanjutnya", previous: "Sebelumnya" }
      },
      order: [[4, "asc"]]
    });
  });
</script>




@endsection
