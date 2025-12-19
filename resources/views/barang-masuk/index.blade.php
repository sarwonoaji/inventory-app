@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')

<div class="flex justify-between items-center mb-6">
    <a href="{{ route('barang-masuk.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Barang Masuk
    </a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-blue-600 text-white text-xs uppercase border-b-2 border-gray-300">
                <tr>
                    <th class="px-6 py-4 border-r border-gray-300">No Transaksi</th>
                    <th class="px-6 py-4 border-r border-gray-300">Tanggal</th>
                    <th class="px-6 py-4 text-center border-r border-gray-300">Jumlah Item</th>
                    <th class="px-6 py-4 text-center border-r border-gray-300">Total Qty</th>
                    <th class="px-6 py-4 text-center border-gray-300">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($barangMasuks as $bm)
                <tr class="hover:bg-blue-50 transition relative"
                    oncontextmenu="event.preventDefault(); showContextMenu('context-{{ $bm->id }}');">
                    <td class="px-6 py-4 border-r border-gray-200 text-center font-medium">
                        {{ $bm->no_transaksi }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-gray-700">
                        {{ \Carbon\Carbon::parse($bm->tanggal)->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">
                        {{ $bm->details->count() }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center font-semibold">
                        {{ $bm->details->sum('qty') }}
                    </td>
                    <td class="px-6 py-4 border-gray-200 text-center space-x-2">
                        <!-- Edit -->
                        @if(!$bm->has_keluar)
                        <a href="{{ route('barang-masuk.edit', $bm->id) }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                            </svg>
                        </a>
                        @else
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed"
                              title="Tidak dapat edit karena sudah ada pengeluaran">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                            </svg>
                        </span>
                        @endif

                        <!-- Hapus -->
                        @if(!$bm->has_keluar)
                        <form action="{{ route('barang-masuk.destroy', $bm->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus barang masuk ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22"/>
                                </svg>
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed"
                              title="Tidak dapat hapus karena sudah ada pengeluaran">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22"/>
                                </svg>
                            </span>
                        @endif

                        <!-- Context menu -->
                       <!-- Context Menu -->
                        <div id="context-{{ $bm->id }}" 
                            class="absolute right-10 top-10 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-50 w-40">
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ route('barang-masuk.show', $bm->id) }}"
                                    class="flex items-center px-4 py-2 hover:bg-blue-50 text-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Detail
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('barang-masuk.pdf', $bm->id) }}" target="_blank"
                                    class="flex items-center px-4 py-2 hover:bg-blue-50 text-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Cetak PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Tombol trigger -->
                        <button onclick="toggleContext('context-{{ $bm->id }}')"
                                class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 transition">
                            ⋮
                        </button>



                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-400">
                        Belum ada transaksi barang masuk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    <style>
        .pagination a, .pagination span {
            color: #2563eb !important; /* blue-600 */
        }
        .pagination .active span {
            background-color: #2563eb !important;
            color: white !important;
        }
    </style>
    {{ $barangMasuks->links() }}
</div>

<script>
    function showContextMenu(id) {
        // Tutup semua context menu lain terlebih dahulu
        document.querySelectorAll('[id^="context-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
        // Buka context menu yang diklik
        document.getElementById(id).classList.remove('hidden');
    }

    function toggleContext(id) {
        const menu = document.getElementById(id);
        const isHidden = menu.classList.contains('hidden');
        
        // Tutup semua context menu lain
        document.querySelectorAll('[id^="context-"]').forEach(m => {
            m.classList.add('hidden');
        });
        
        // Toggle menu yang diklik
        if (isHidden) {
            menu.classList.remove('hidden');
        }
    }

    // Tutup saat klik di luar
    document.addEventListener('click', function(e) {
        document.querySelectorAll('[id^="context-"]').forEach(menu => {
            if (!menu.contains(e.target) && !e.target.closest('button')) {
                menu.classList.add('hidden');
            }
        });
    });
</script>
@endsection
