@forelse($barangs as $barang)
<tr class="hover:bg-blue-50 transition">
    <td class="px-6 py-4 border-r border-gray-200 font-medium text-gray-700">
        {{ $barang->kode_barang }}
    </td>

    <td class="px-6 py-4 border-r border-gray-200 text-gray-700">
        {{ $barang->nama_barang }}
    </td>

    <td class="px-6 py-4 text-center border-r border-gray-200">
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
            {{ $barang->stok > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $barang->stok }}
        </span>
    </td>

    <td class="px-6 py-4 text-center space-x-3">
        <!-- Edit -->
        <a href="{{ route('barang.edit', $barang->id) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                </svg>
            </a>

        <!-- Hapus -->
        @if ($barang->barang_masuk_details_count == 0)
        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="inline"
            onsubmit="return confirm('Yakin hapus barang?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                </svg>
            </button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="py-10 text-center text-gray-400">
        Belum ada data barang
    </td>
</tr>
@endforelse
