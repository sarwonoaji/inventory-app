<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\BarangKeluarDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuks = BarangMasuk::with('details.barang')->latest()->paginate(10);

        // Check for each if has keluar
        foreach ($barangMasuks as $bm) {
            $barangIds = $bm->details->pluck('barang_id');
            $bm->has_keluar = BarangKeluarDetail::whereIn('barang_id', $barangIds)->exists();
        }

        return view('barang-masuk.index', compact('barangMasuks'));
    }

    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang-masuk.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'barang_id.*' => 'required',
            'qty.*' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {

            $barangMasuk = BarangMasuk::create([
                'no_transaksi' => 'BM-' . time(),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);

            foreach ($request->barang_id as $i => $barangId) {
                BarangMasukDetail::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $barangId,
                    'qty' => $request->qty[$i]
                ]);

                // UPDATE STOK
                Barang::where('id', $barangId)
                    ->increment('stok', $request->qty[$i]);
            }
            ActivityLogger::log('create_barang_masuk', $barangMasuk, ['count' => count($request->barang_id)]);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil disimpan');
    }

    public function show($id)
    {
        $barangMasuk = BarangMasuk::with('details.barang')->findOrFail($id);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit($id)
    {
        $barangMasuk = BarangMasuk::with('details')->findOrFail($id);
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'barang_id.*' => 'required',
            'qty.*' => 'required|integer|min:1'
        ]);

        $barangMasuk = BarangMasuk::with('details')->findOrFail($id);

        // Check if any barang has been used in barang keluar
        $barangIds = $barangMasuk->details->pluck('barang_id');
        if (BarangKeluarDetail::whereIn('barang_id', $barangIds)->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat mengedit barang masuk karena sudah ada pengeluaran untuk barang terkait');
        }

        DB::transaction(function () use ($request, $id) {

            // Kembalikan stok lama
            foreach ($barangMasuk->details as $detail) {
                Barang::where('id', $detail->barang_id)
                    ->decrement('stok', $detail->qty);
            }

            // Hapus detail lama
            $barangMasuk->details()->delete();

            // Update master
            $barangMasuk->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);

            // Tambah detail baru & update stok
            foreach ($request->barang_id as $i => $barangId) {
                BarangMasukDetail::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $barangId,
                    'qty' => $request->qty[$i]
                ]);

                Barang::where('id', $barangId)
                    ->increment('stok', $request->qty[$i]);
            }
            ActivityLogger::log('update_barang_masuk', $barangMasuk, ['count' => count($request->barang_id)]);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::with('details')->findOrFail($id);

        // Check if any barang has been used in barang keluar
        $barangIds = $barangMasuk->details->pluck('barang_id');
        if (BarangKeluarDetail::whereIn('barang_id', $barangIds)->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat menghapus barang masuk karena sudah ada pengeluaran untuk barang terkait');
        }

        DB::transaction(function () use ($id) {

            // Kembalikan stok
            foreach ($barangMasuk->details as $detail) {
                Barang::where('id', $detail->barang_id)
                    ->decrement('stok', $detail->qty);
            }

            // Hapus master + detail
            $barangMasuk->details()->delete();
            $barangMasuk->delete();
            ActivityLogger::log('delete_barang_masuk', $barangMasuk, null);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dihapus');
    }

    public function pdf($id)
    {
        $barangMasuk = BarangMasuk::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('barang-masuk.pdf', compact('barangMasuk'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('BarangMasuk-'.$barangMasuk->no_transaksi.'.pdf');
    }
}
