<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Nota - {{ $penjualan->kode_penjualan }}</title>
  <style>
    /* RESET & BASE */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      color: #000;
      background: #fff;
      margin: 20px;
    }

    /* CONTAINER */
    .container {
      max-width: 800px;
      margin: 0 auto;
    }

    /* HEADER & FOOTER */
    .header, .footer {
      text-align: center;
      margin-bottom: 15px;
    }
    .header h2 {
      font-size: 20px;
      margin-bottom: 5px;
    }

    /* TABLES */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    table, th, td {
      border: 1px solid #000;
    }
    th, td {
      padding: 6px 8px;
      vertical-align: top;
    }
    th {
      text-align: center;
    }
    .text-right {
      text-align: right;
      white-space: nowrap; /* Agar "Rp." menempel dengan angkanya */
    }
    .no-border {
      border: none !important;
    }

    /* SUMMARY TABLE */
    .summary-table td {
      border: none;
      padding: 3px;
    }
  </style>
</head>
<body onload="window.print();">
  <div class="container">
    <!-- HEADER -->
    <div class="header">
      <h2>NOTA PENJUALAN</h2>
      <p><strong>Kode Transaksi:</strong> {{ $penjualan->kode_penjualan }}</p>
      <p><strong>Tanggal:</strong> {{ $penjualan->tgl }}</p>
    </div>

    <!-- INFO MEMBER (Jika Ada) -->
    <table>
      <tr>
        <td>
          <strong>Member:</strong>
          {{ $penjualan->member_id ? $penjualan->member->nama : 'Non Membership' }}
        </td>
        <td class="text-right">
          @if($penjualan->member_id)
            @php
              // Hitung total poin yang dikurangi dari diskon
              $poin_deducted = 0;
              foreach($detail as $item) {
                if($item->discount_applied && $item->discount_id) {
                  $disc = \App\Models\Discount::find($item->discount_id);
                  if($disc && $disc->needed_poin) {
                    $poin_deducted += $disc->needed_poin;
                  }
                }
              }
              // Poin yang didapat dari pembayaran (di-set di session oleh controller)
              $poin_gained = session('poin_tambahan', 0);
              // Poin saat ini
              $current_poin = $penjualan->member->poin;
              // Asumsi: poin awal = poin sekarang + poin yang didapat - poin yang dikurangi
              $initial_poin = $current_poin + $poin_gained - $poin_deducted;
            @endphp
            <strong>Poin Awal:</strong> {{ $initial_poin }}
          @else
            -
          @endif
        </td>
      </tr>
      @if($penjualan->member_id)
      <tr>
        <td><strong>Poin Dikurangi (Diskon):</strong> {{ $poin_deducted }}</td>
        <td class="text-right"><strong>Poin Didapat:</strong> {{ $poin_gained }}</td>
      </tr>
      <tr>
        <td colspan="2" class="text-right">
          <strong>Total Poin Sekarang:</strong> {{ $penjualan->member->poin }}
        </td>
      </tr>
      @endif
    </table>

    <!-- TABEL DETAIL PENJUALAN -->
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Produk</th>
          <th>Qty</th>
          <th>Harga Dasar</th>
          <th>PPN (12%)</th>
          <th>Harga Akhir</th>
          <th>Subtotal</th>
          <th>Keterangan Diskon</th>
        </tr>
      </thead>
      <tbody>
        @php
          $total = 0;
          $totalPPN = 0;
        @endphp
        @foreach($detail as $index => $item)
          @php
            $hargaDasar = $item->produk->price;
            // PPN 12% dari hargaDasar
            $ppn = $hargaDasar * 0.12;
            $hargaAkhir = $hargaDasar + $ppn;

            // Jika harga_satuan sudah termasuk markup, sesuaikan rumus
            // atau cukup pakai $hargaDasar + PPN sesuai logika Anda.

            $originalSubtotal = $hargaAkhir * $item->qty;
            $subtotal = $item->discount_applied ? $item->subtotal : $originalSubtotal;
            $total += $subtotal;
            $totalPPN += $ppn * $item->qty;
          @endphp
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->produk->name }}</td>
            <td class="text-right">{{ $item->qty }}</td>
            <td class="text-right">Rp. {{ number_format($hargaDasar, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($ppn, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($hargaAkhir, 2, ',', '.') }}</td>
            <td class="text-right">
              @if($item->discount_applied)
                <del>Rp. {{ number_format($originalSubtotal, 2, ',', '.') }}</del><br>
                <span>Rp. {{ number_format($subtotal, 2, ',', '.') }}</span>
              @else
                Rp. {{ number_format($subtotal, 2, ',', '.') }}
              @endif
            </td>
            <td>
              @if($item->discount_applied)
                @php $disc = \App\Models\Discount::find($item->discount_id); @endphp
                Diskon: {{ $disc->discount }}% <br>
                Potongan: Rp. {{ number_format($item->discount_amount, 2, ',', '.') }} <br>
                @if($disc->needed_poin > 0)
                  Poin dikurangi: {{ $disc->needed_poin }} poin
                @endif
              @else
                -
              @endif
            </td>
          </tr>
        @endforeach
        <tr>
          <td colspan="5" class="text-right"><strong>Total Harga</strong></td>
          <td colspan="2" class="text-right"><strong>Rp. {{ number_format($total, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="5" class="text-right"><strong>Total PPN (12%)</strong></td>
          <td colspan="2" class="text-right">
            <strong>Rp. {{ number_format($totalPPN, 2, ',', '.') }}</strong>
          </td>
          <td></td>
        </tr>
      </tbody>
    </table>

    <!-- RINGKASAN PEMBAYARAN -->
    <table class="summary-table" style="width:100%;">
      <tr>
        <td class="no-border text-right"><strong>Total Harga:</strong></td>
        <td class="no-border text-right">Rp. {{ number_format($penjualan->total_harga, 2, ',', '.') }}</td>
      </tr>
      <tr>
        <td class="no-border text-right"><strong>Total Bayar:</strong></td>
        <td class="no-border text-right">Rp. {{ number_format($penjualan->bayar, 2, ',', '.') }}</td>
      </tr>
      <tr>
        <td class="no-border text-right"><strong>Kembalian:</strong></td>
        <td class="no-border text-right">
          Rp. {{ number_format($penjualan->bayar - $penjualan->total_harga, 2, ',', '.') }}
        </td>
      </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
      <p>Terima Kasih</p>
      <p><strong>Kasir.IDon</strong></p>
    </div>
  </div>
</body>
</html>
