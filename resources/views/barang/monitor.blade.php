@extends('layouts.app')

@section('title', 'Monitoring Barang')

@section('content')

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Monitoring Barang</h2>

    <form method="GET" class="grid grid-cols-3 gap-4 mb-6">
        <input type="hidden" name="barang_id" id="barang-id" value="{{ request('barang_id') ?? '' }}">
        <div class="relative">
            <label class="block text-sm text-gray-600">Nama Barang</label>
            <input type="text" name="nama" id="nama-autocomplete" value="{{ $nama ?? '' }}" class="mt-1 w-full border rounded px-3 py-2" autocomplete="off">
            <div id="suggestions" class="absolute left-0 right-0 bg-white border mt-1 rounded shadow z-50 hidden"></div>
        </div>

        <div>
            <label class="block text-sm text-gray-600">Start Date</label>
            <input type="date" name="start_date" value="{{ $start ?? '' }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-gray-600">End Date</label>
            <input type="date" name="end_date" value="{{ $end ?? '' }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="col-span-3 mt-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Cari</button>
            <a href="{{ route('barang.monitor') }}" class="ml-2 text-sm text-gray-600 underline">Reset</a>
            @if(!empty(request()->query()))
            <a href="{{ request()->fullUrlWithQuery([]) }}" id="export-csv" class="ml-4 inline-block bg-gray-100 px-3 py-2 rounded text-sm">CSV</a>
            <a href="{{ request()->fullUrlWithQuery([]) }}" id="export-pdf" class="ml-2 inline-block bg-gray-100 px-3 py-2 rounded text-sm">PDF</a>
            @endif

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const csv = document.getElementById('export-csv');
                const pdf = document.getElementById('export-pdf');
                const params = new URLSearchParams(window.location.search);
                if (csv) {
                    const url = new URL('{{ route('barang.monitor.csv') }}', window.location.origin);
                    url.search = params.toString();
                    csv.href = url.toString();
                }
                if (pdf) {
                    const url = new URL('{{ route('barang.monitor.pdf') }}', window.location.origin);
                    url.search = params.toString();
                    pdf.href = url.toString();
                }
            });
            </script>
        </div>
    </form>

    @if(empty($searched))
        <div class="py-12 text-center text-gray-500">Silakan masukkan kriteria pencarian di atas untuk menampilkan hasil.</div>
    @else
        @if(count($results) === 0)
            <div class="py-6 text-center text-gray-500">Tidak ada barang ditemukan</div>
        @else
            <div class="mb-6">
                <h3 class="font-semibold mb-2">Ringkasan Hasil</h3>
                <table class="w-full text-sm border-collapse">
                    <thead class="text-xs text-gray-600">
                        <tr>
                            <th class="text-left">Kode</th>
                            <th class="text-left">Nama</th>
                            <th class="text-right">Total Masuk</th>
                            <th class="text-right">Total Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                        <tr>
                            <td class="py-2">{{ $row['barang']->kode_barang }}</td>
                            <td class="py-2">{{ $row['barang']->nama_barang }}</td>
                            <td class="py-2 text-right">{{ $row['total_masuk'] }}</td>
                            <td class="py-2 text-right">{{ $row['total_keluar'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($results as $row)
            <div class="mb-6">
                <h4 class="font-medium">{{ $row['barang']->nama_barang }} — {{ $row['barang']->kode_barang }}</h4>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <div class="text-sm text-gray-600 mb-2">Barang Masuk</div>
                        <table class="w-full text-sm border-collapse">
                            <thead class="text-xs text-gray-600">
                                <tr>
                                    <th class="text-left">Tanggal</th>
                                    <th class="text-left">No Transaksi</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($row['masuks'] as $m)
                                <tr>
                                    <td class="py-1">{{ optional($m->barangMasuk)->tanggal }}</td>
                                    <td class="py-1">{{ optional($m->barangMasuk)->no_transaksi }}</td>
                                    <td class="py-1 text-right">{{ $m->qty }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="py-2 text-gray-500">Tidak ada</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <div class="text-sm text-gray-600 mb-2">Barang Keluar</div>
                        <table class="w-full text-sm border-collapse">
                            <thead class="text-xs text-gray-600">
                                <tr>
                                    <th class="text-left">Tanggal</th>
                                    <th class="text-left">No Transaksi</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($row['keluars'] as $k)
                                <tr>
                                    <td class="py-1">{{ optional($k->barangKeluar)->tanggal }}</td>
                                    <td class="py-1">{{ optional($k->barangKeluar)->no_transaksi }}</td>
                                    <td class="py-1 text-right">{{ $k->qty }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="py-2 text-gray-500">Tidak ada</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('nama-autocomplete');
    const suggestions = document.getElementById('suggestions');
    const searchUrl = '{{ route('barang.search') }}';

    const debounce = (fn, delay = 250) => {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
    };

    const renderSuggestions = (items) => {
        if (!items || items.length === 0) {
            suggestions.classList.add('hidden');
            suggestions.innerHTML = '';
            return;
        }
        suggestions.classList.remove('hidden');
        suggestions.innerHTML = items.map(i => `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="${i.id}" data-label="${i.label}">${i.label}</div>`).join('');
        suggestions.querySelectorAll('div').forEach(el => {
            el.addEventListener('click', () => {
                input.value = el.dataset.label;
                // set hidden barang id if present in dataset
                const id = el.dataset.id;
                const hidden = document.getElementById('barang-id');
                if (hidden) hidden.value = id ?? '';
                suggestions.classList.add('hidden');
            });
        });
    };

    input?.addEventListener('input', debounce(function(e) {
        const q = e.target.value.trim();
        // clear previously selected barang_id when user types
        const hidden = document.getElementById('barang-id');
        if (hidden) hidden.value = '';
        if (!q) { renderSuggestions([]); return; }

        fetch(searchUrl + '?q=' + encodeURIComponent(q) + '&suggest=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                renderSuggestions(data.suggestions || []);
            })
            .catch(err => {
                console.error('Suggestion error', err);
                renderSuggestions([]);
            });
    }));

    // close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.classList.add('hidden');
        }
    });
});
</script>

@endsection
