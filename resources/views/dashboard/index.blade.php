@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Welcome Section -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Selamat Datang di Inventory App</h1>
    <p class="text-gray-600">{{ Carbon\Carbon::now()->format('l, d F Y') }}</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Total Barang -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Barang</p>
                <h2 class="text-3xl font-bold">{{ $totalBarang }}</h2>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Stok -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Stok</p>
                <h2 class="text-3xl font-bold">{{ number_format($totalStok) }}</h2>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM6 9a1 1 0 112 0 1 1 0 01-2 0zm6 0a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stok Habis -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm">Stok Habis</p>
                <h2 class="text-3xl font-bold">{{ $stokHabis }}</h2>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Stok Menipis</p>
                <h2 class="text-3xl font-bold">{{ $stokMenipis }}</h2>
                <p class="text-xs text-orange-100 mt-1">≤ 10 unit</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    <!-- Stok Trend Chart -->
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Stok (6 Bulan)</h3>
        <canvas id="stokChart" width="400" height="200"></canvas>
    </div>

    <!-- Top Barang Keluar Chart -->
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Barang Keluar</h3>
        <canvas id="topBarangChart" width="400" height="200"></canvas>
    </div>

    <!-- Reorder Alerts Chart -->
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Reorder Alerts</h3>
        <canvas id="reorderChart" width="400" height="200"></canvas>
    </div>

</div>

<!-- Quick Actions -->
<div class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('barang.create') }}" 
           class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
            <svg class="w-8 h-8 text-blue-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Tambah Barang</span>
        </a>
        
        <a href="{{ route('barang-masuk.create') }}" 
           class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition group">
            <svg class="w-8 h-8 text-green-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Barang Masuk</span>
        </a>
        
        <a href="{{ route('barang-keluar.create') }}" 
           class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition group">
            <svg class="w-8 h-8 text-orange-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Barang Keluar</span>
        </a>
        
        <a href="{{ route('barang.index') }}" 
           class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
            <svg class="w-8 h-8 text-purple-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Lihat Inventory</span>
        </a>
    </div>
</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Activity Chart -->
    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas 7 Hari Terakhir</h3>
            <div class="space-y-4">
                <!-- Simple chart with bars -->
                @foreach($chartData as $data)
                <div class="flex items-center space-x-4">
                    <div class="w-12 text-xs text-gray-600">{{ $data['date'] }}</div>
                    <div class="flex-1 flex space-x-2">
                        <div class="flex items-center space-x-2 flex-1">
                            <div class="text-xs text-blue-600 w-16">Masuk: {{ $data['masuk'] }}</div>
                            <div class="bg-gray-200 rounded-full h-2 flex-1">
                                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $data['masuk'] > 0 ? min(($data['masuk'] / max(max(array_column($chartData, 'masuk')), 1)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 flex-1">
                            <div class="text-xs text-orange-600 w-16">Keluar: {{ $data['keluar'] }}</div>
                            <div class="bg-gray-200 rounded-full h-2 flex-1">
                                <div class="bg-orange-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $data['keluar'] > 0 ? min(($data['keluar'] / max(max(array_column($chartData, 'keluar')), 1)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Today's Stats -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Masuk Hari Ini</p>
                        <p class="text-xl font-bold text-gray-800">{{ $barangMasukHariIni }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-orange-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Keluar Hari Ini</p>
                        <p class="text-xl font-bold text-gray-800">{{ $barangKeluarHariIni }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="space-y-6">
        
        <!-- Alert Stok Menipis -->
        @if($barangMenipis->count() > 0)
        <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl">
            <div class="flex items-center space-x-2 mb-3">
                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <h4 class="font-semibold text-orange-800">Stok Menipis</h4>
            </div>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach($barangMenipis->take(5) as $barang)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700 truncate">{{ Str::limit($barang->nama_barang, 20) }}</span>
                    <span class="bg-orange-200 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">{{ $barang->stok }}</span>
                </div>
                @endforeach
                @if($barangMenipis->count() > 5)
                <div class="text-center">
                    <a href="{{ route('barang.index') }}" class="text-orange-600 hover:text-orange-800 text-xs">
                        Lihat {{ $barangMenipis->count() - 5 }} lainnya
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Activities -->
        <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-3">Aktivitas Terbaru</h4>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($recentBarangMasuk->take(3) as $bm)
                <div class="flex items-center space-x-3 text-sm">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-gray-700">Barang Masuk</p>
                        <p class="text-xs text-gray-500">{{ $bm->details->sum('qty') }} item • {{ $bm->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                @endforelse
                
                @forelse($recentBarangKeluar->take(3) as $bk)
                <div class="flex items-center space-x-3 text-sm">
                    <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-gray-700">Barang Keluar</p>
                        <p class="text-xs text-gray-500">{{ $bk->details->sum('qty') }} item • {{ $bk->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                @endforelse
                
                @if($recentBarangMasuk->isEmpty() && $recentBarangKeluar->isEmpty())
                <p class="text-gray-500 text-sm text-center py-4">Belum ada aktivitas</p>
                @endif
            </div>
        </div>

        <!-- Weekly Summary -->
        <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Minggu Ini</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Barang Masuk</span>
                    <span class="font-semibold text-blue-600">{{ $barangMasukMingguIni }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Barang Keluar</span>
                    <span class="font-semibold text-orange-600">{{ $barangKeluarMingguIni }}</span>
                </div>
                <hr class="border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Selisih</span>
                    <span class="font-bold {{ ($barangMasukMingguIni - $barangKeluarMingguIni) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $barangMasukMingguIni > $barangKeluarMingguIni ? '+' : '' }}{{ $barangMasukMingguIni - $barangKeluarMingguIni }}
                    </span>
                </div>
            </div>
        </div>
        
    </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stok Trend Chart
    const stokCtx = document.getElementById('stokChart').getContext('2d');
    const stokData = @json($stokChartData);
    new Chart(stokCtx, {
        type: 'line',
        data: {
            labels: stokData.map(item => item.month),
            datasets: [{
                label: 'Total Stok',
                data: stokData.map(item => item.stok),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Barang Keluar Chart
    const topCtx = document.getElementById('topBarangChart').getContext('2d');
    const topData = @json($topBarangKeluar);
    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: topData.map(item => item.nama_barang.substring(0, 15) + (item.nama_barang.length > 15 ? '...' : '')),
            datasets: [{
                label: 'Qty Keluar',
                data: topData.map(item => item.total_keluar),
                backgroundColor: 'rgba(249, 115, 22, 0.8)',
                borderColor: 'rgb(249, 115, 22)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Reorder Alerts Chart
    const reorderCtx = document.getElementById('reorderChart').getContext('2d');
    const reorderData = @json($reorderAlerts);
    new Chart(reorderCtx, {
        type: 'doughnut',
        data: {
            labels: reorderData.map(item => item.nama_barang.substring(0, 15) + (item.nama_barang.length > 15 ? '...' : '')),
            datasets: [{
                data: reorderData.map(item => item.stok),
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
