@extends('layouts.app')

@section('title', 'Edit Barang Masuk')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Barang Masuk</h1>
        <p class="text-sm text-gray-500">Ubah pencatatan barang masuk ke gudang</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

        <form action="{{ route('barang-masuk.update', $barangMasuk->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div class="mb-6 max-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal
                </label>
                <input type="date" name="tanggal" value="{{ $barangMasuk->tanggal }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       required>
            </div>
            <div class="mt-8 flex justify-end">
            <button type="button"
                    onclick="addRow()"
                    class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                + Tambah Baris
            </button>
            </div>
            <!-- Detail Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="border border-gray-200 px-4 py-3 text-left">Barang</th>
                            <th class="border border-gray-200 px-4 py-3 text-center w-32">Qty</th>
                            <th class="border border-gray-200 px-4 py-3 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-body">
                        @foreach($barangMasuk->details as $detail)
                        <tr>
                            <td class="border border-gray-200 px-4 py-2">
                                <select name="barang_id[]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}" {{ $detail->barang_id == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="border border-gray-200 px-4 py-2 text-center">
                                <input type="number" name="qty[]" value="{{ $detail->qty }}"
                                       class="w-full rounded-lg border-gray-300 text-center focus:border-blue-500 focus:ring-blue-500"
                                       min="1" required>
                            </td>

                            <td class="border border-gray-200 px-4 py-2 text-center">
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeRow(this)">✕</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

           <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('barang-masuk.index') }}"
                class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg shadow hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>

<!-- SCRIPT -->
<script>
    function addRow() {
        const tbody = document.getElementById('detail-body');
        const row = tbody.rows[0].cloneNode(true);
        row.querySelectorAll('select, input').forEach(el => el.value = '');
        tbody.appendChild(row);
    }

    function removeRow(button) {
        const tbody = document.getElementById('detail-body');
        if (tbody.rows.length > 1) {
            button.closest('tr').remove();
        }
    }
</script>

@endsection
