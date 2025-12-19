@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')

<div class="flex justify-between items-center mb-6">
    <!-- Tombol Tambah di kiri -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('barang.create') }}"
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Tambah
    </a>

        <a href="{{ route('barang.import.form') }}" class="inline-flex items-center bg-green-600 text-white px-3 py-2 rounded-lg shadow hover:bg-green-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12v8M8 16l4-4 4 4" />
            </svg>
            Import
        </a>

        <a href="{{ route('barang.import.template') }}" class="inline-flex items-center bg-gray-100 text-gray-800 px-3 py-2 rounded-lg shadow hover:bg-gray-200 transition">
            Download Template
        </a>
    </div>

    <!-- Search form di kanan -->
    <form method="GET" class="flex items-center" id="search-form" data-search-url="{{ route('barang.search') }}">
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                </svg>
            </span>
            <input type="text" name="search" id="search-input" value="{{ $search }}"
                   placeholder="Cari kode / nama barang..."
                   class="rounded-l-md border border-gray-300 pl-10 pr-4 py-2 focus:ring focus:ring-blue-200">
        </div>

        @if(!empty($search))
        <button type="button" id="clear-search" class="ml-2 text-sm text-gray-600 underline">Hapus</button>
        @endif
    </form>

    <script>
        document.getElementById('clear-search')?.addEventListener('click', function() {
            const input = document.getElementById('search-input');
            if (!input) return;
            input.value = '';
            // trigger input event so live-search picks up the change
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    </script>
</div>



@if(session('success'))
<div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
    {{ session('success') }}
</div>
@endif



@php
    function sortIcon($column, $sort, $direction) {
        if ($sort !== $column) {
            return '↕';
        }
        return $direction === 'asc' ? '↑' : '↓';
    }

    function sortDirection($column, $sort, $direction) {
        return $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
    }
@endphp

<div class="bg-white rounded-xl shadow border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">

        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-blue-600 text-white text-xs uppercase border-b-2 border-gray-300">
                <tr>
                    <th class="px-6 py-4 border-r border-gray-300">
                        <a href="?sort=kode_barang&direction={{ sortDirection('kode_barang', $sort, $direction) }}"
                           class="flex items-center justify-between hover:text-gray-200">
                            Kode
                            <span>{{ sortIcon('kode_barang', $sort, $direction) }}</span>
                        </a>
                    </th>

                    <th class="px-6 py-4 border-r border-gray-300">
                        <a href="?sort=nama_barang&direction={{ sortDirection('nama_barang', $sort, $direction) }}"
                           class="flex items-center justify-between hover:text-gray-200">
                            Nama Barang
                            <span>{{ sortIcon('nama_barang', $sort, $direction) }}</span>
                        </a>
                    </th>

                    <th class="px-6 py-4 text-center border-r border-gray-300">
                        <a href="?sort=stok&direction={{ sortDirection('stok', $sort, $direction) }}"
                           class="flex items-center justify-center gap-1 hover:text-gray-200">
                            Stok
                            <span>{{ sortIcon('stok', $sort, $direction) }}</span>
                        </a>
                    </th>

                    <th class="px-6 py-4 text-center border-r border-gray-300">
                        QR Code
                    </th>

                    <th class="px-6 py-4 text-center border-gray-300">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody id="table-body" class="divide-y divide-gray-200">
                @include('barang._rows', ['barangs' => $barangs])
            </tbody>
        </table>

    </div>
</div>



    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        const tbody = document.getElementById('table-body');
        const searchUrl = form?.dataset?.searchUrl;

        const debounce = (fn, delay = 300) => {
            let t;
            return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
        };

        const setLoading = (on=true) => {
            if (!tbody) return;
            if (on) {
                tbody.innerHTML = '<tr><td colspan="4" class="py-6 text-center text-gray-500">Memuat...</td></tr>';
            }
        };

        if (input && searchUrl) {
            input.addEventListener('input', debounce(function(e) {
                const q = e.target.value || '';
                setLoading(true);

                fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => {
                        if (!r.ok) throw new Error('Network response was not ok');
                        return r.json();
                    })
                    .then(data => {
                        if (data && typeof data.rows !== 'undefined') {
                            tbody.innerHTML = data.rows || '<tr><td colspan="4" class="py-6 text-center text-gray-500">Tidak ada hasil</td></tr>';
                        } else {
                            tbody.innerHTML = '<tr><td colspan="4" class="py-6 text-center text-gray-500">Tidak ada hasil</td></tr>';
                        }
                    })
                    .catch(err => {
                        // If fetch fails, fallback to full form submit so search still works
                        console.error('Live search failed, falling back to full submit', err);
                        form.submit();
                    })
                    .finally(() => setLoading(false));
            }, 300));
        }
    });
    </script>





{{-- PAGINATION --}}
<div class="mt-4">
    {{ $barangs->links() }}
</div>

@endsection
