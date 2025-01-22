<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota</title>
    <link href="style/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('style/lib/owlcarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}">

    <!-- Customized Bootstrap Stylesheet -->
    <link rel="stylesheet" href="{{ asset('style/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/css/style.css') }}">
</head>
<body class="bg-white">
    <div class="container">
        <h2 class="text-center mb-5 mt-5">Nota Penjualan</h2><hr>    
        <div class="row">
            <div class="col-6">
                <ul class="list-unstyled">
                    <li><strong>Pelanggan : {{ $penjualan->pelanggan->nama }}</strong></li>
                    <li><strong>Telp : {{ $penjualan->pelanggan->telp }}</strong></li>
                    <li><strong>Alamat : {{ $penjualan->pelanggan->alamat }}</strong></li>
                </ul>
            </div>
            <div class="col-6 text-end">
                <ul class="list-unstyled">
                    <li><strong>Kode Transaksi : {{ $penjualan->kode_penjualan }}</strong></li>
                    <li><strong>Tanggal Transaksi : {{ $penjualan->tgl }}</strong></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Produk</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail as $item)
                            <tr>
                                <td >{{ $loop->iteration }}</td>
                                <td>{{ $item->produk->nama }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>Rp. {{ number_format($item->produk->harga) }}</td>
                                <td>Rp. {{ number_format($item->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-12 text-end">
                <ul class="list-unstyled">
                    <li><strong>Total Harga : {{ number_format($penjualan->total_harga) }}</strong></li>
                    <li><strong>Total Bayar : {{ number_format($penjualan->bayar) }}</strong></li>
                    <li><strong>Total Kembalian : {{ number_format($penjualan->bayar - $penjualan->total_harga) }}</strong></li>
                </ul>
            </div>
        </div>
        <h2 class="text-center">Terima Kasih</h2>
    </div>
</body>
</html>