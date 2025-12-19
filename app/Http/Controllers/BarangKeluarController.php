<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendReorderNotification;
use Illuminate\Support\Facades\Log;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $barangKeluars = BarangKeluar::with('details.barang')->latest()->paginate(10);
        return view('barang-keluar.index', compact('barangKeluars'));
    }

    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang-keluar.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'barang_id.*' => 'required',
            'qty.*' => 'required|integer|min:1'
        ]);

        $errors = [];
        foreach ($request->barang_id as $i => $barangId) {
            $barang = Barang::find($barangId);
            if (!$barang) {
                $errors[] = "Barang ID $barangId tidak ditemukan.";
                continue;
            }

            $qty = isset($request->qty[$i]) ? (int)$request->qty[$i] : 0;
            if ($qty <= 0) {
                $errors[] = "Qty untuk barang {$barang->nama_barang} harus lebih besar dari 0.";
                continue;
            }

            if ($qty > $barang->stok) {
                $errors[] = "Qty keluar untuk barang {$barang->nama_barang} tidak boleh melebihi stok ({$barang->stok}).";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors($errors);
        }


        DB::transaction(function () use ($request) {

            $barangKeluar = BarangKeluar::create([
                'no_transaksi' => 'BK-' . time(),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);

            foreach ($request->barang_id as $i => $barangId) {
                BarangKeluarDetail::create([
                    'barang_keluar_id' => $barangKeluar->id,
                    'barang_id' => $barangId,
                    'qty' => $request->qty[$i]
                ]);

                // UPDATE STOK (kurangi stok)
                Barang::where('id', $barangId)
                    ->decrement('stok', $request->qty[$i]);

                // Check for reorder
                $barang = Barang::find($barangId);
                Log::info('Barang after decrement: ' . $barang->nama_barang . ' Stok: ' . $barang->stok . ' Min: ' . $barang->min_stok);
                if ($barang && $barang->stok <= $barang->min_stok) {
                    Log::info('Dispatching reorder job for: ' . $barang->nama_barang);
                    SendReorderNotification::dispatch($barang);
                }
            }
            ActivityLogger::log('create_barang_keluar', $barangKeluar, ['count' => count($request->barang_id)]);
        });

        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil disimpan');
    }

    public function show($id)
    {
        $barangKeluar = BarangKeluar::with('details.barang')->findOrFail($id);
        return view('barang-keluar.show', compact('barangKeluar'));
    }

    public function edit($id)
    {
        $barangKeluar = BarangKeluar::with('details')->findOrFail($id);
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang-keluar.edit', compact('barangKeluar', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'barang_id.*' => 'required',
            'qty.*' => 'required|integer|min:1'
        ]);
        // Collect errors first before doing DB transaction
        $barangKeluar = BarangKeluar::with('details')->findOrFail($id);

        // Build a map of original quantities per barang from existing details
        $originalMap = [];
        foreach ($barangKeluar->details as $detail) {
            if (!isset($originalMap[$detail->barang_id])) {
                $originalMap[$detail->barang_id] = 0;
            }
            $originalMap[$detail->barang_id] += $detail->qty;
        }

        // Build a map of requested totals per barang to handle duplicate rows
        $requestedTotals = [];
        foreach ($request->barang_id as $i => $barangId) {
            $qty = isset($request->qty[$i]) ? (int)$request->qty[$i] : 0;
            if (!isset($requestedTotals[$barangId])) {
                $requestedTotals[$barangId] = 0;
            }
            $requestedTotals[$barangId] += $qty;
        }

        $errors = [];
        foreach ($requestedTotals as $barangId => $totalQty) {
            $barang = Barang::find($barangId);
            if (!$barang) {
                $errors[] = "Barang ID $barangId tidak ditemukan.";
                continue;
            }

            // Available stock = current stock + original allocated qty for this barang
            $available = $barang->stok + ($originalMap[$barangId] ?? 0);

            if ($totalQty > $available) {
                $errors[] = "Total qty untuk barang {$barang->nama_barang} ({$totalQty}) melebihi stok tersedia ({$available}).";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        DB::transaction(function () use ($request, $barangKeluar) {
            // Kembalikan stok lama (tambah kembali)
            foreach ($barangKeluar->details as $detail) {
                Barang::where('id', $detail->barang_id)
                    ->increment('stok', $detail->qty);
            }

            // Hapus detail lama
            $barangKeluar->details()->delete();

            // Update master
            $barangKeluar->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);

            // Tambah detail baru & update stok
            foreach ($request->barang_id as $i => $barangId) {
                $qty = (int)$request->qty[$i];
                BarangKeluarDetail::create([
                    'barang_keluar_id' => $barangKeluar->id,
                    'barang_id' => $barangId,
                    'qty' => $qty
                ]);

                // Kurangi stok
                Barang::where('id', $barangId)
                    ->decrement('stok', $qty);
            }
            ActivityLogger::log('update_barang_keluar', $barangKeluar, ['count' => count($request->barang_id)]);

            // Check for reorder after update
            foreach ($request->barang_id as $i => $barangId) {
                $barang = Barang::find($barangId);
                Log::info('Barang after update decrement: ' . $barang->nama_barang . ' Stok: ' . $barang->stok . ' Min: ' . $barang->min_stok);
                if ($barang && $barang->stok <= $barang->min_stok) {
                    Log::info('Dispatching reorder job for: ' . $barang->nama_barang);
                    SendReorderNotification::dispatch($barang);
                }
            }
        });

        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $barangKeluar = BarangKeluar::with('details')->findOrFail($id);

            // Kembalikan stok (tambah kembali)
            foreach ($barangKeluar->details as $detail) {
                Barang::where('id', $detail->barang_id)
                    ->increment('stok', $detail->qty);
            }

            // Hapus master + detail
            $barangKeluar->details()->delete();
            $barangKeluar->delete();
            ActivityLogger::log('delete_barang_keluar', $barangKeluar, null);
        });

        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil dihapus');
    }

    public function pdf($id)
    {
        $barangKeluar = BarangKeluar::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('barang-keluar.pdf', compact('barangKeluar'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('BarangKeluar-'.$barangKeluar->no_transaksi.'.pdf');
    }
}
