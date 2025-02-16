<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nota Penjualan - {{ $penjualan->kode_penjualan }}</title>
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
    .container {
      max-width: 800px;
      margin: 0 auto;
    }
    .header, .footer {
      text-align: center;
      margin-bottom: 15px;
    }
    .header h2 {
      font-size: 20px;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .header p {
      margin: 2px 0;
    }
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
      white-space: nowrap;
    }
    .no-border {
      border: none !important;
    }
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

    <!-- INFO MEMBER -->
    <table>
      <tr>
        <td>
          <strong>Member:</strong>
          {{ $penjualan->member_id ? $penjualan->member->nama : 'Non Membership' }}
        </td>
        <td class="text-right">
          @if($penjualan->member_id)
            @php
              // Ambil data poin dari penjualan
              $used_poin = $penjualan->used_poin ?? 0;
              $get_poin  = $penjualan->get_poin ?? 0;
              $current_point = $penjualan->member->poin;
              // Poin Awal = current poin + used_poin - get_poin
              $initial_point = $current_point + $used_poin - $get_poin;
            @endphp
            <strong>Poin Awal:</strong> {{ $initial_point }}
          @else
            -
          @endif
        </td>
      </tr>
      @if($penjualan->member_id)
      <tr>
        <td><strong>Poin Dikurangi (Diskon):</strong> {{ $used_poin }}</td>
        <td class="text-right"><strong>Poin Didapat:</strong> {{ $get_poin }}</td>
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
          <th>Harga </th>
          <th>PPN (12%)</th>
          <th>Harga Akhir</th>
          <th>Subtotal</th>
          <th>Diskon</th>
        </tr>
      </thead>
      <tbody>
        @php
          $total = 0;
          $totalPPN = 0;
          $totalDiscount = 0;
        @endphp
        @foreach($detail as $index => $item)
          @php
            // Ambil harga jual final yang sudah tersimpan di detail->harga_satuan (sudah termasuk PPN)
            $finalPrice = $item->harga_satuan;
            // Harga setelah markup = finalPrice dibagi 1.12
            $hargaSetelahMarkup = $finalPrice / 1.12;
            // PPN per unit = 12% dari harga setelah markup
            $ppn_per_unit = $hargaSetelahMarkup * 0.12;
            // Subtotal asli per item = finalPrice * qty
            $subtotal_original = $finalPrice * $item->qty;
            // Jika ada diskon, gunakan nilai discount_amount dari detail
            $subtotal = $item->discount_applied ? $item->subtotal : $subtotal_original;
            $total += $subtotal;
            $totalPPN += $ppn_per_unit * $item->qty;
            if($item->discount_applied) {
              $totalDiscount += $item->discount_amount;
            }
          @endphp
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->produk->name }}</td>
            <td class="text-right">{{ $item->qty }}</td>
            <td class="text-right">Rp. {{ number_format($hargaSetelahMarkup, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($ppn_per_unit, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($finalPrice, 2, ',', '.') }}</td>
            <td class="text-right">
              @if($item->discount_applied)
                <del>Rp. {{ number_format($subtotal_original, 2, ',', '.') }}</del><br>
                <span>Rp. {{ number_format($subtotal, 2, ',', '.') }}</span>
              @else
                Rp. {{ number_format($subtotal_original, 2, ',', '.') }}
              @endif
            </td>
            <td>
              @if($item->discount_applied)
                @php $disc = \App\Models\Discount::find($item->discount_id); @endphp
                Diskon: {{ $disc->discount }}%<br>
                Potongan: Rp. {{ number_format($item->discount_amount, 2, ',', '.') }}<br>
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
          <td colspan="6" class="text-right"><strong>Total Harga</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($total, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="6" class="text-right"><strong>Total PPN (12%)</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($totalPPN, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="6" class="text-right"><strong>Total Diskon</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($totalDiscount, 2, ',', '.') }}</strong></td>
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
        <td class="no-border text-right">Rp. {{ number_format($penjualan->bayar - $penjualan->total_harga, 2, ',', '.') }}</td>
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
