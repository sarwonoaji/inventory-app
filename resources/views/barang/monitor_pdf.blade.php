<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monitoring Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; }
        h2 { margin-bottom: 6px; }
    </style>
</head>
<body>
    <h2>Monitoring Barang</h2>
    <div>Periode: {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</div>
    <br />

    @foreach($results as $row)
        <h3>{{ $row['barang']->kode_barang }} - {{ $row['barang']->nama_barang }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Keluar Tgl</th>
                    <th>Keluar No</th>
                    <th>Keluar Qty</th>
                    <th>Masuk Tgl</th>
                    <th>Masuk No</th>
                    <th>Masuk Qty</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $start = $start ?? null;
                    $initialMasuk = 0;
                    $initialKeluar = 0;
                    if ($start) {
                        $initialMasuk = \App\Models\BarangMasukDetail::where('barang_id', $row['barang']->id)
                            ->whereHas('barangMasuk', fn($q)=> $q->where('tanggal', '<', $start))
                            ->sum('qty');
                        $initialKeluar = \App\Models\BarangKeluarDetail::where('barang_id', $row['barang']->id)
                            ->whereHas('barangKeluar', fn($q)=> $q->where('tanggal', '<', $start))
                            ->sum('qty');
                    }
                    $balance = $initialMasuk - $initialKeluar;
                    $events = [];
                    foreach ($row['masuks'] as $m) {
                        $events[] = ['type' => 'masuk', 'date' => optional($m->barangMasuk)->tanggal, 'no' => optional($m->barangMasuk)->no_transaksi, 'qty' => $m->qty];
                    }
                    foreach ($row['keluars'] as $k) {
                        $events[] = ['type' => 'keluar', 'date' => optional($k->barangKeluar)->tanggal, 'no' => optional($k->barangKeluar)->no_transaksi, 'qty' => $k->qty];
                    }
                    usort($events, function($a,$b){
                        $da = $a['date'] ? \Carbon\Carbon::parse($a['date']) : \Carbon\Carbon::now();
                        $db = $b['date'] ? \Carbon\Carbon::parse($b['date']) : \Carbon\Carbon::now();
                        return $da <=> $db;
                    });
                @endphp

                @if(count($events) === 0)
                <tr>
                    <td colspan="6"></td>
                    <td class="text-right">{{ $balance }}</td>
                </tr>
                @endif

                @foreach($events as $ev)
                    @if($ev['type'] === 'masuk')
                        @php $balance += $ev['qty']; @endphp
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $ev['date'] }}</td>
                            <td>{{ $ev['no'] }}</td>
                            <td class="text-right">{{ $ev['qty'] }}</td>
                            <td class="text-right">{{ $balance }}</td>
                        </tr>
                    @else
                        @php $balance -= $ev['qty']; @endphp
                        <tr>
                            <td>{{ $ev['date'] }}</td>
                            <td>{{ $ev['no'] }}</td>
                            <td class="text-right">{{ $ev['qty'] }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ $balance }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>