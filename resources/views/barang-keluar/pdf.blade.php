<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barang Keluar {{ $barangKeluar->no_transaksi }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        h2, p { margin: 0; }
    </style>
</head>
<body>

<h2>Barang Keluar</h2>
<p>No Transaksi: {{ $barangKeluar->no_transaksi }}</p>
<p>Tanggal: {{ \Carbon\Carbon::parse($barangKeluar->tanggal)->format('d-m-Y') }}</p>
<p>Keterangan: {{ $barangKeluar->keterangan ?? '-' }}</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($barangKeluar->details as $i => $detail)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $detail->barang->kode_barang }}</td>
            <td>{{ $detail->barang->nama_barang }}</td>
            <td>{{ $detail->qty }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align: right; margin-top: 20px;">
    Total Qty: {{ $barangKeluar->details->sum('qty') }}
</p>

</body>
</html>
