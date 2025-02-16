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
          <th>Harga Barang</th>
          <th>Markup</th>
          <th>Harga Setelah Markup</th>
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
          $totalMarkup = 0;
        @endphp
        @foreach($detailPenjualan as $index => $item)
          @php
            // Harga Barang (belum di markup)
            $hargaBarang = $item->produk->price;
            // Harga Setelah Markup = harga_satuan / 1.12 (asumsi harga_satuan adalah harga jual final termasuk PPN)
            $hargaSetelahMarkup = $item->harga_satuan / 1.12;
            // Markup per unit
            $markupPerUnit = $hargaSetelahMarkup - $hargaBarang;
            // PPN per unit = 12% dari harga setelah markup
            $ppn_per_unit = $hargaSetelahMarkup * 0.12;
            // Harga Akhir per unit = harga_satuan (langsung)
            $hargaAkhir = $item->harga_satuan;
            // Subtotal asli per item = hargaAkhir * qty
            $subtotal_original = $hargaAkhir * $item->qty;
            // Jika ada diskon, gunakan nilai discount_amount dari detail; jika tidak, subtotal asli
            $subtotal = $item->discount_applied ? $item->subtotal : $subtotal_original;

            // Akumulasi
            $total += $subtotal;
            $totalPPN += $ppn_per_unit * $item->qty;
            if($item->discount_applied) {
              $totalDiscount += $item->discount_amount;
            }
            $totalMarkup += $markupPerUnit * $item->qty;
          @endphp
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->produk->name }}</td>
            <td class="text-right">{{ $item->qty }}</td>
            <td class="text-right">Rp. {{ number_format($hargaBarang, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($markupPerUnit, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($hargaSetelahMarkup, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($ppn_per_unit, 2, ',', '.') }}</td>
            <td class="text-right">Rp. {{ number_format($hargaAkhir, 2, ',', '.') }}</td>
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
        <!-- Total Markup -->
        <tr>
          <td colspan="8" class="text-right"><strong>Total Markup</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($totalMarkup, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <!-- Total Diskon -->
        <tr>
          <td colspan="8" class="text-right"><strong>Total Diskon</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($totalDiscount, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <!-- Total Pendapatan: Total Markup dikurangi Total Diskon -->
        @php
          $totalPendapatan = $totalMarkup - $totalDiscount;
          $respon = $totalPendapatan >= 0 ? 'Untung' : 'Rugi';
          $nominalRespon = abs($totalPendapatan);
        @endphp
        <tr>
          <td colspan="8" class="text-right"><strong>Total Pendapatan</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($totalPendapatan, 2, ',', '.') }}</strong></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="8" class="text-right"><strong>{{ $respon }}</strong></td>
          <td class="text-right"><strong>Rp. {{ number_format($nominalRespon, 2, ',', '.') }}</strong></td>
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

  </div>
</body>
</html>
