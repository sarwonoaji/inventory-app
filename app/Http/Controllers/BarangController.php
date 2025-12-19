<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'nama_barang';
        $direction = $request->direction ?? 'asc';

        $barangs = Barang::withCount('barangMasukDetails')->when($search, function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%$search%")
                ->orWhere('nama_barang', 'like', "%$search%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('barang.index', compact('barangs', 'search', 'sort', 'direction'));
    }

    public function search(Request $request)
    {
        $q = $request->query('q') ?? $request->query('search');
        $barangs = Barang::withCount('barangMasukDetails')->when($q, function ($qbl) use ($q) {
                $qbl->where('kode_barang', 'like', "%$q%")
                    ->orWhere('nama_barang', 'like', "%$q%");
            })
            ->orderBy('nama_barang', 'asc')
            ->limit(50)
            ->get();

        // Render rows partial
        $rows = view('barang._rows', compact('barangs'))->render();

        // If debug flag is sent, return additional info
        if ($request->boolean('debug')) {
            Log::debug('Barang search debug', ['q' => $q, 'count' => $barangs->count()]);
            return response()->json([
                'rows' => $rows,
                'query' => $q,
                'count' => $barangs->count(),
            ]);
        }

        // If suggest flag is present, return simple JSON suggestions
        if ($request->boolean('suggest')) {
            $suggestions = $barangs->map(fn($b) => ['id' => $b->id, 'label' => $b->kode_barang . ' - ' . $b->nama_barang]);
            return response()->json(['suggestions' => $suggestions]);
        }

        return response()->json(['rows' => $rows]);
    }

    public function monitor(Request $request)
    {
        $nama = $request->query('nama');
        $barangId = $request->query('barang_id');
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        // If barang_id provided, load that specific barang; otherwise search by name (partial)
        if ($barangId) {
            $barangs = \App\Models\Barang::withCount('barangMasukDetails')->where('id', $barangId)->get();
        } else {
            $queryBarangs = \App\Models\Barang::withCount('barangMasukDetails');
            if ($nama) {
                $queryBarangs->where('nama_barang', 'like', "%$nama%");
            }
            $barangs = $queryBarangs->get();
        }

        // If no filters provided (no nama/barangId/start/end), do not load anything
        $searched = ($barangId || $nama || $start || $end);
        $results = [];
        if ($searched) {
        foreach ($barangs as $barang) {
            $masuks = \App\Models\BarangMasukDetail::where('barang_id', $barang->id)
                ->when($start, function ($q) use ($start) { $q->whereHas('barangMasuk', fn($s)=> $s->where('tanggal', '>=', $start)); })
                ->when($end, function ($q) use ($end) { $q->whereHas('barangMasuk', fn($s)=> $s->where('tanggal', '<=', $end)); })
                ->with('barangMasuk')
                ->get();

            $keluars = \App\Models\BarangKeluarDetail::where('barang_id', $barang->id)
                ->when($start, function ($q) use ($start) { $q->whereHas('barangKeluar', fn($s)=> $s->where('tanggal', '>=', $start)); })
                ->when($end, function ($q) use ($end) { $q->whereHas('barangKeluar', fn($s)=> $s->where('tanggal', '<=', $end)); })
                ->with('barangKeluar')
                ->get();

            // compute totals
            $totalMasuk = $masuks->sum('qty');
            $totalKeluar = $keluars->sum('qty');

            $results[] = [
                'barang' => $barang,
                'masuks' => $masuks,
                'keluars' => $keluars,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
            ];
        }
        }

        return view('barang.monitor', compact('results', 'nama', 'start', 'end', 'searched'));
    }

    protected function buildMonitorResults(Request $request)
    {
        // Reuse monitor logic to build results array
        $nama = $request->query('nama');
        $barangId = $request->query('barang_id');
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        $searched = ($barangId || $nama || $start || $end);
        $results = [];
        if ($searched) {
            if ($barangId) {
                $barangs = \App\Models\Barang::withCount('barangMasukDetails')->where('id', $barangId)->get();
            } else {
                $queryBarangs = \App\Models\Barang::withCount('barangMasukDetails');
                if ($nama) {
                    $queryBarangs->where('nama_barang', 'like', "%$nama%");
                }
                $barangs = $queryBarangs->get();
            }

            foreach ($barangs as $barang) {
                $masuks = \App\Models\BarangMasukDetail::where('barang_id', $barang->id)
                    ->when($start, function ($q) use ($start) { $q->whereHas('barangMasuk', fn($s)=> $s->where('tanggal', '>=', $start)); })
                    ->when($end, function ($q) use ($end) { $q->whereHas('barangMasuk', fn($s)=> $s->where('tanggal', '<=', $end)); })
                    ->with('barangMasuk')
                    ->get();

                $keluars = \App\Models\BarangKeluarDetail::where('barang_id', $barang->id)
                    ->when($start, function ($q) use ($start) { $q->whereHas('barangKeluar', fn($s)=> $s->where('tanggal', '>=', $start)); })
                    ->when($end, function ($q) use ($end) { $q->whereHas('barangKeluar', fn($s)=> $s->where('tanggal', '<=', $end)); })
                    ->with('barangKeluar')
                    ->get();

                $results[] = [
                    'barang' => $barang,
                    'masuks' => $masuks,
                    'keluars' => $keluars,
                    'total_masuk' => $masuks->sum('qty'),
                    'total_keluar' => $keluars->sum('qty'),
                ];
            }
        }

        return ['searched' => $searched, 'results' => $results, 'nama' => $nama, 'start' => $start, 'end' => $end];
    }

    public function exportCsv(Request $request)
    {
        $data = $this->buildMonitorResults($request);
        if (!$data['searched']) {
            return redirect()->route('barang.monitor')->with('success', 'Silakan masukkan kriteria pencarian sebelum mengekspor.');
        }

        $filename = 'monitoring_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data, $request) {
            $out = fopen('php://output', 'w');
            // Header: Kode, Nama, Keluar(Tanggal, No, Qty), Masuk(Tanggal, No, Qty), Balance
            fputcsv($out, ['Kode','Nama','Keluar Tanggal','Keluar No','Keluar Qty','Masuk Tanggal','Masuk No','Masuk Qty','Balance']);

            foreach ($data['results'] as $row) {
                $barang = $row['barang'];

                // compute initial balance before start date
                $start = $request->query('start_date');
                $initialMasuk = 0;
                $initialKeluar = 0;
                if ($start) {
                    $initialMasuk = \App\Models\BarangMasukDetail::where('barang_id', $barang->id)
                        ->whereHas('barangMasuk', fn($q)=> $q->where('tanggal', '<', $start))
                        ->sum('qty');
                    $initialKeluar = \App\Models\BarangKeluarDetail::where('barang_id', $barang->id)
                        ->whereHas('barangKeluar', fn($q)=> $q->where('tanggal', '<', $start))
                        ->sum('qty');
                }
                $balance = $initialMasuk - $initialKeluar;

                // Build merged timeline
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

                // If there were no events but we might still show initial balance row
                if (count($events) === 0) {
                    fputcsv($out, [$barang->kode_barang, $barang->nama_barang, '','','','', '', '', $balance]);
                }

                foreach ($events as $ev) {
                    if ($ev['type'] === 'masuk') {
                        $balance += $ev['qty'];
                        fputcsv($out, [$barang->kode_barang, $barang->nama_barang, '', '', '', $ev['date'], $ev['no'], $ev['qty'], $balance]);
                    } else {
                        $balance -= $ev['qty'];
                        fputcsv($out, [$barang->kode_barang, $barang->nama_barang, $ev['date'], $ev['no'], $ev['qty'], '', '', '', $balance]);
                    }
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->buildMonitorResults($request);
        if (!$data['searched']) {
            return redirect()->route('barang.monitor')->with('success', 'Silakan masukkan kriteria pencarian sebelum mengekspor.');
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('barang.monitor_pdf', $data);
        return $pdf->stream('monitoring_' . now()->format('Ymd_His') . '.pdf');
    }

    public function importForm()
    {
        return view('barang.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');

        // Lazy-load PhpSpreadsheet to avoid hard dependency until needed
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            return redirect()->back()->with('error', 'PhpSpreadsheet tidak ditemukan. Jalankan: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Expected columns: A=kode_barang, B=nama_barang, C=satuan, D=stok (optional), E=keterangan (optional)
        $created = 0;
        $errors = [];
        $seenKodes = [];
        $seenNama = [];
        foreach ($rows as $index => $row) {
            // skip header row (assume first row is header)
            if ($index === 1) continue;

            $kode = trim($row['A'] ?? '');
            $nama = trim($row['B'] ?? '');
            $satuan = trim($row['C'] ?? '');
            $stok = $row['D'] ?? null;
            $keterangan = trim($row['D'] ?? '');

            // Skip rows that have none of the required fields (A,B,C) — treat rows with only optional columns (like keterangan) as empty
            $requiredEmpty = true;
            foreach (['A','B','C'] as $col) {
                if (trim($row[$col] ?? '') !== '') { $requiredEmpty = false; break; }
            }
            if ($requiredEmpty) continue;

            $rowErrors = [];
            if (empty($kode)) $rowErrors[] = 'kode_barang kosong';
            if (empty($nama)) $rowErrors[] = 'nama_barang kosong';
            if (empty($satuan)) $rowErrors[] = 'satuan kosong';

            // duplicates in file
            if (!empty($kode)) {
                if (in_array(strtolower($kode), $seenKodes)) {
                    $rowErrors[] = 'kode_barang duplikat di file';
                } else {
                    $seenKodes[] = strtolower($kode);
                }
            }
            if (!empty($nama)) {
                if (in_array(strtolower($nama), $seenNama)) {
                    $rowErrors[] = 'nama_barang duplikat di file';
                } else {
                    $seenNama[] = strtolower($nama);
                }
            }

            // check against DB
            if (!empty($kode) && \App\Models\Barang::where('kode_barang', $kode)->exists()) {
                $rowErrors[] = 'kode_barang sudah ada di database';
            }
            if (!empty($nama) && \App\Models\Barang::where('nama_barang', $nama)->exists()) {
                $rowErrors[] = 'nama_barang sudah ada di database';
            }

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $index, 'kode' => $kode, 'nama' => $nama, 'errors' => $rowErrors];
                continue;
            }

            // Upsert by kode_barang to avoid duplicates
            $barang = \App\Models\Barang::firstOrNew(['kode_barang' => $kode]);
            $barang->nama_barang = $nama;
            $barang->satuan = $satuan;
            // For new records, default stok to 0. Do not overwrite existing stok.
            if (! $barang->exists) {
                $barang->stok = 0;
            }
            $barang->keterangan = $keterangan;
            $barang->save();
            $created++;
        }

        $redirect = redirect()->route('barang.index')->with('success', "Import selesai. $created item diproses.");
        if (!empty($errors)) {
            $redirect = redirect()->route('barang.import.form')->with('import_errors', $errors)->with('success', "Import selesai. $created item diproses. Beberapa baris dilewati.");
        }

        return $redirect;
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            return redirect()->back()->with('error', 'PhpSpreadsheet tidak ditemukan. Jalankan: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $preview = [];
        $errors = [];
        // Collect to check duplicates within the file
        $seenKodes = [];
        $seenNama = [];
        foreach ($rows as $index => $row) {
            if ($index === 1) continue; // header


            $kode = trim($row['A'] ?? '');
            $nama = trim($row['B'] ?? '');
            $satuan = trim($row['C'] ?? '');
            $keterangan = trim($row['D'] ?? '');

            // Skip rows that have none of the required fields
            $requiredEmpty = true;
            foreach (['A','B','C'] as $col) {
                if (trim($row[$col] ?? '') !== '') { $requiredEmpty = false; break; }
            }
            if ($requiredEmpty) continue;

            $rowErrors = [];
            if (empty($kode)) $rowErrors[] = 'kode_barang kosong';
            if (empty($nama)) $rowErrors[] = 'nama_barang kosong';
            if (empty($satuan)) $rowErrors[] = 'satuan kosong';

            // Check duplicates within uploaded file
            if (!empty($kode)) {
                if (in_array(strtolower($kode), $seenKodes)) {
                    $rowErrors[] = 'kode_barang duplikat di file';
                } else {
                    $seenKodes[] = strtolower($kode);
                }
            }
            if (!empty($nama)) {
                if (in_array(strtolower($nama), $seenNama)) {
                    $rowErrors[] = 'nama_barang duplikat di file';
                } else {
                    $seenNama[] = strtolower($nama);
                }
            }

            // Check against existing DB entries
            if (!empty($kode) && \App\Models\Barang::where('kode_barang', $kode)->exists()) {
                $rowErrors[] = 'kode_barang sudah ada di database';
            }
            if (!empty($nama) && \App\Models\Barang::where('nama_barang', $nama)->exists()) {
                $rowErrors[] = 'nama_barang sudah ada di database';
            }

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $index, 'kode' => $kode, 'nama' => $nama, 'errors' => $rowErrors];
                continue;
            }

            $preview[] = ['row' => $index, 'kode' => $kode, 'nama' => $nama, 'satuan' => $satuan, 'keterangan' => $keterangan];
        }

        // If there are edited preview rows in session prefer them (user returned to preview)
        $edited = session('import_preview_edited');
        if (!empty($edited)) {
            $preview = $edited;
        } else {
            // store a non-persistent preview (for immediate navigation) but do not persist errors
            session(['import_preview' => $preview]);
        }

        // Flash errors only for this request so they don't persist after refresh/navigation
        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }

        return view('barang.import_preview', compact('preview', 'errors'));
    }

    public function savePreviewEdits(Request $request)
    {
        $rows = $request->input('rows');
        if (!is_array($rows)) {
            return response()->json(['ok' => false, 'message' => 'Invalid rows']);
        }

        // Store edited preview in session
        session(['import_preview_edited' => $rows]);
        return response()->json(['ok' => true]);
    }

    public function importConfirm(Request $request)
    {
        $rows = $request->input('rows', []);
        if (empty($rows)) {
            return redirect()->route('barang.import.form')->with('error', 'Tidak ada data untuk diproses. Lakukan preview terlebih dahulu.');
        }

        $errors = [];
        $validRows = [];
        $seenKodes = [];
        $seenNama = [];
        foreach ($rows as $i => $r) {
            $rowNum = $r['row'] ?? ($i+2);
            $kode = trim($r['kode'] ?? '');
            $nama = trim($r['nama'] ?? '');
            $satuan = trim($r['satuan'] ?? '');
            $keterangan = trim($r['keterangan'] ?? '');

            $rowErrors = [];
            if (empty($kode)) $rowErrors[] = 'kode_barang kosong';
            if (empty($nama)) $rowErrors[] = 'nama_barang kosong';
            if (empty($satuan)) $rowErrors[] = 'satuan kosong';

            // duplicates in submitted rows
            if (!empty($kode)) {
                if (in_array(strtolower($kode), $seenKodes)) {
                    $rowErrors[] = 'kode_barang duplikat di file';
                } else {
                    $seenKodes[] = strtolower($kode);
                }
            }
            if (!empty($nama)) {
                if (in_array(strtolower($nama), $seenNama)) {
                    $rowErrors[] = 'nama_barang duplikat di file';
                } else {
                    $seenNama[] = strtolower($nama);
                }
            }

            // check against DB (in case records were added while previewing)
            if (!empty($kode) && \App\Models\Barang::where('kode_barang', $kode)->exists()) {
                $rowErrors[] = 'kode_barang sudah ada di database';
            }
            if (!empty($nama) && \App\Models\Barang::where('nama_barang', $nama)->exists()) {
                $rowErrors[] = 'nama_barang sudah ada di database';
            }

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'kode' => $kode, 'nama' => $nama, 'errors' => $rowErrors];
            } else {
                $validRows[] = ['row' => $rowNum, 'kode' => $kode, 'nama' => $nama, 'satuan' => $satuan, 'keterangan' => $keterangan];
            }
        }

        if (!empty($errors)) {
            // return to preview showing errors and the edited rows - flash errors so they don't persist
            session()->flash('import_errors', $errors);
            session(['import_preview_edited' => $rows]);
            return view('barang.import_preview', ['preview' => $rows, 'errors' => $errors]);
        }

        $created = 0;
        foreach ($validRows as $row) {
            $barang = \App\Models\Barang::firstOrNew(['kode_barang' => $row['kode']]);
            $barang->nama_barang = $row['nama'];
            $barang->satuan = $row['satuan'];
            if (! $barang->exists) {
                $barang->stok = 0;
            }
            $barang->keterangan = $row['keterangan'] ?? '';
            $barang->save();
            $created++;
        }

        // Clear preview and any edited draft
        session()->forget('import_preview');
        session()->forget('import_preview_edited');

        return redirect()->route('barang.index')->with('success', "Import selesai. $created item diproses.");
    }

    public function downloadImportTemplate()
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return redirect()->back()->with('error', 'PhpSpreadsheet tidak ditemukan. Jalankan: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Barang');

        // Header (no stok column)
        $sheet->setCellValue('A1', 'kode_barang');
        $sheet->setCellValue('B1', 'nama_barang');
        $sheet->setCellValue('C1', 'satuan');
        $sheet->setCellValue('D1', 'keterangan');

        // Example rows (no stok column)
        $sheet->setCellValue('A2', 'BRG001');
        $sheet->setCellValue('B2', 'Contoh Barang');
        $sheet->setCellValue('C2', 'Pcs');
        $sheet->setCellValue('D2', 'Contoh keterangan');

        // Apply bold to header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Set column widths
        foreach (['A','B','C','D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Data validation for satuan (example: Pcs, Kg, L)
        $validation = $sheet->getCell('C2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Invalid input');
        $validation->setError('Value is not in the list');
        $validation->setFormula1('"Pcs,Kg,L"');

        // Apply same validation to a reasonable range (C2:C1000)
        for ($r = 2; $r <= 1000; $r++) {
            $sheet->getCell("C$r")->setDataValidation(clone $validation);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'barang_import_template.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'satuan' => 'required',
        ]);

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'stok' => 0,
            'keterangan' => $request->keterangan,
            'min_stok' => $request->min_stok ?? 0,
            'supplier_email' => $request->supplier_email,
        ]);

        ActivityLogger::log('create_barang', null, ['kode_barang' => $request->kode_barang, 'nama_barang' => $request->nama_barang]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'satuan' => 'required',
        ]);

        $barang->update($request->all());

        ActivityLogger::log('update_barang', $barang, $request->only(['kode_barang','nama_barang','satuan','keterangan']));

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        ActivityLogger::log('delete_barang', $barang, null);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus');
    }

    public function scanQr()
    {
        return view('barang.scan-qr');
    }

    public function processQr(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $barang = Barang::where('kode_barang', $request->qr_code)->first();

        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json([
            'nama_barang' => $barang->nama_barang,
            'kode_barang' => $barang->kode_barang,
            'stok' => $barang->stok,
            'satuan' => $barang->satuan,
            'keterangan' => $barang->keterangan,
            'min_stok' => $barang->min_stok,
        ]);
    }

    public function generateQr(Barang $barang)
    {
        $qrCode = new QrCode($barang->kode_barang);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
