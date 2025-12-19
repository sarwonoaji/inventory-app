<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalBarang = Barang::count();
        $totalStok = Barang::sum('stok');
        $stokHabis = Barang::where('stok', 0)->count();
        $stokMenipis = Barang::where('stok', '>', 0)->where('stok', '<=', 10)->count();
        
        // Transaksi hari ini
        $today = Carbon::today();
        $barangMasukHariIni = BarangMasuk::whereDate('tanggal', $today)->count();
        $barangKeluarHariIni = BarangKeluar::whereDate('tanggal', $today)->count();
        
        // Transaksi minggu ini
        $weekStart = Carbon::now()->startOfWeek();
        $barangMasukMingguIni = BarangMasuk::where('tanggal', '>=', $weekStart)->count();
        $barangKeluarMingguIni = BarangKeluar::where('tanggal', '>=', $weekStart)->count();
        
        // Recent activities
        $recentBarangMasuk = BarangMasuk::with('details.barang')
            ->latest()
            ->take(5)
            ->get();
            
        $recentBarangKeluar = BarangKeluar::with('details.barang')
            ->latest()
            ->take(5)
            ->get();
            
        // Barang dengan stok menipis
        $barangMenipis = Barang::where('stok', '>', 0)
            ->where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->take(10)
            ->get();
            
        // Data untuk chart stok per bulan (6 bulan terakhir)
        $stokChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $stok = Barang::whereYear('created_at', $month->year)
                         ->whereMonth('created_at', $month->month)
                         ->sum('stok');
            
            $stokChartData[] = [
                'month' => $month->format('M Y'),
                'stok' => $stok
            ];
        }
        
        // Top 5 barang keluar
        $topBarangKeluar = DB::table('barang_keluar_details')
            ->join('barangs', 'barang_keluar_details.barang_id', '=', 'barangs.id')
            ->select('barangs.nama_barang', DB::raw('SUM(barang_keluar_details.qty) as total_keluar'))
            ->groupBy('barang_keluar_details.barang_id', 'barangs.nama_barang')
            ->orderBy('total_keluar', 'desc')
            ->take(5)
            ->get();
        
        // Reorder alerts (barang dengan stok <= min_stok atau 10)
        $reorderAlerts = Barang::where('stok', '<=', DB::raw('COALESCE(min_stok, 10)'))
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();
        
        // Data untuk chart 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $masuk = BarangMasuk::whereDate('tanggal', $date)->count();
            $keluar = BarangKeluar::whereDate('tanggal', $date)->count();
            
            $chartData[] = [
                'date' => $date->format('d/m'),
                'masuk' => $masuk,
                'keluar' => $keluar
            ];
        }
        
        return view('dashboard.index', compact(
            'totalBarang',
            'totalStok', 
            'stokHabis',
            'stokMenipis',
            'barangMasukHariIni',
            'barangKeluarHariIni',
            'barangMasukMingguIni',
            'barangKeluarMingguIni',
            'recentBarangMasuk',
            'recentBarangKeluar',
            'barangMenipis',
            'chartData',
            'stokChartData',
            'topBarangKeluar',
            'reorderAlerts'
        ));
    }
}

