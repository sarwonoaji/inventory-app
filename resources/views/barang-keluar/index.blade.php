@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('content')

<div class="flex justify-between items-center mb-6">
    <a href="{{ route('barang-keluar.create') }}"
       class="bg-orange-600 text-white px-4 py-2 rounded-lg shadow hover:bg-orange-700 transition">
        + Barang Keluar
    </a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-orange-600 text-white text-xs uppercase border-b-2 border-gray-300">
                <tr>
                    <th class="px-6 py-4 border-r border-gray-300">No Transaksi</th>
                    <th class="px-6 py-4 border-r border-gray-300">Tanggal</th>
                    <th class="px-6 py-4 text-center border-r border-gray-300">Jumlah Item</th>
                    <th class="px-6 py-4 text-center border-r border-gray-300">Total Qty</th>
                    <th class="px-6 py-4 text-center border-gray-300">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($barangKeluars as $bk)
                <tr class="hover:bg-orange-50 transition relative"
                    oncontextmenu="event.preventDefault(); showContextMenu('context-{{ $bk->id }}');">
                    <td class="px-6 py-4 border-r border-gray-200 text-center font-medium">
                        {{ $bk->no_transaksi }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-gray-700">
                        {{ \Carbon\Carbon::parse($bk->tanggal)->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">
                        {{ $bk->details->count() }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center font-semibold">
                        {{ $bk->details->sum('qty') }}
                    </td>
                    <td class="px-6 py-4 border-gray-200 text-center space-x-2">
                        <!-- Edit -->
                        <a href="{{ route('barang-keluar.edit', $bk->id) }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                            </svg>
                        </a>

                        <!-- Hapus -->
                        <form action="{{ route('barang-keluar.destroy', $bk->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus barang keluar ini?')">
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

                        <!-- Context Menu -->
                        <div id="context-{{ $bk->id }}" 
                            class="absolute right-10 top-10 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-50 w-40">
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ route('barang-keluar.show', $bk->id) }}"
                                    class="flex items-center px-4 py-2 hover:bg-orange-50 text-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Detail
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('barang-keluar.pdf', $bk->id) }}" target="_blank"
                                    class="flex items-center px-4 py-2 hover:bg-orange-50 text-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Cetak PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Tombol trigger -->
                        <button onclick="toggleContext('context-{{ $bk->id }}')"
                                class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 transition">
                            ⋮
                        </button>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-400">
                        Belum ada transaksi barang keluar
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
