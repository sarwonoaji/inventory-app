@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Barang Keluar</h1>
        <p class="text-sm text-gray-500">Pencatatan barang keluar dari gudang</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

        @if($errors->any())
        <div class="mb-4">
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-4">
            <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded">
                {{ session('success') }}
            </div>
        </div>
        @endif

        <form id="form-keluar" action="{{ route('barang-keluar.store') }}" method="POST">
            @csrf

            <!-- Tanggal -->
            <div class="mb-6 max-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal
                </label>
                <input type="date" name="tanggal"
                       class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                       required>
            </div>

            <!-- Keterangan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan (Opsional)
                </label>
                <textarea name="keterangan" rows="2"
                       class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                       placeholder="Masukkan keterangan..."></textarea>
            </div>

            <!-- Add Row -->
            <div class="mt-8 flex justify-end">
                <button type="button"
                        onclick="addRow()"
                        class="mt-4 inline-flex items-center text-orange-600 hover:text-orange-800 text-sm font-medium">
                    + Tambah Baris
                </button>
            </div>

            <!-- Detail Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="border border-gray-200 px-4 py-3 text-left">Barang</th>
                            <th class="border border-gray-200 px-4 py-3 text-center w-24">Stok</th>
                            <th class="border border-gray-200 px-4 py-3 text-center w-32">Qty</th>
                            <th class="border border-gray-200 px-4 py-3 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-body">
                        <tr>
                            <td class="border border-gray-200 px-4 py-2">
                                <select name="barang_id[]"
                                        class="barang-select w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                                        onchange="updateStok(this)"
                                        required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}">
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="border border-gray-200 px-4 py-2 text-center stok-display">
                                -
                            </td>

                            <td class="border border-gray-200 px-4 py-2 text-center">
                                <input type="number" name="qty[]"
                                       class="qty-input w-full rounded-lg border-gray-300 text-center focus:border-orange-500 focus:ring-orange-500"
                                       min="1" required>
                                <div class="row-error text-red-600 text-xs mt-1 hidden"></div>
                            </td>

                            <td class="border border-gray-200 px-4 py-2 text-center">
                                <button type="button"
                                        class="text-red-600 hover:text-red-800"
                                        onclick="removeRow(this)">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('barang-keluar.index') }}"
                   class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg shadow hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button class="bg-orange-600 text-white px-6 py-2 rounded-lg shadow hover:bg-orange-700 transition">
                    Save
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

        row.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
        row.querySelectorAll('input').forEach(el => el.value = '');
        row.querySelector('.stok-display').textContent = '-';
        row.querySelector('.row-error').classList.add('hidden');

        // Reattach onchange handler for select
        const select = row.querySelector('select');
        select.addEventListener('change', function() { updateStok(this); clearRowError(this); });

        tbody.appendChild(row);
    }

    function removeRow(button) {
        const tbody = document.getElementById('detail-body');
        if (tbody.rows.length > 1) {
            button.closest('tr').remove();
        }
    }

    function updateStok(select) {
        const row = select.closest('tr');
        const stokDisplay = row.querySelector('.stok-display');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            stokDisplay.textContent = selectedOption.dataset.stok || '0';
        } else {
            stokDisplay.textContent = '-';
        }
        clearRowError(select);
    }

    function clearRowError(el) {
        const row = el.closest('tr');
        const err = row.querySelector('.row-error');
        if (err) { err.textContent = ''; err.classList.add('hidden'); }
    }

    function validateRows() {
        const tbody = document.getElementById('detail-body');
        let hasError = false;

        Array.from(tbody.rows).forEach(row => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('.qty-input');
            const stok = parseInt(select.options[select.selectedIndex]?.dataset?.stok || 0, 10);
            const errEl = row.querySelector('.row-error');
            errEl.textContent = '';
            errEl.classList.add('hidden');

            if (!select.value) {
                errEl.textContent = 'Pilih barang.';
                errEl.classList.remove('hidden');
                hasError = true;
                return;
            }

            const qty = parseInt(qtyInput.value || 0, 10);
            if (!qty || qty <= 0) {
                errEl.textContent = 'Qty harus > 0.';
                errEl.classList.remove('hidden');
                hasError = true;
                return;
            }

            if (qty > stok) {
                errEl.textContent = `Qty (${qty}) melebihi stok (${stok}).`;
                errEl.classList.remove('hidden');
                hasError = true;
                return;
            }
        });

        return !hasError;
    }

    document.getElementById('form-keluar').addEventListener('submit', function(e) {
        if (!validateRows()) {
            e.preventDefault();
            // do not refresh
        }
    });
</script>

@endsection
