@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Import Barang dari Excel</h1>
            <div class="text-sm text-gray-600">Template: <a href="{{ route('barang.import.template') }}" class="text-blue-600">XLSX</a> / <a href="{{ asset('templates/barang_import_template.csv') }}" class="text-blue-600">CSV</a></div>
        </div>

        @if(session('error'))
            <div class="mb-4 rounded px-4 py-3 bg-red-50 text-red-700">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="mb-4 rounded px-4 py-3 bg-green-50 text-green-700">{{ session('success') }}</div>
        @endif

        <form action="{{ route('barang.import.preview') }}" method="POST" enctype="multipart/form-data" id="import-form">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih file Excel</label>

                <label for="file-input" class="relative flex items-center justify-center border-2 border-dashed border-gray-200 rounded-lg p-8 cursor-pointer hover:border-blue-300">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16v-4m0 0L3 9m4 3l4-4m6 8v-4m0 0l4-3m-4 3l-4-4" />
                        </svg>
                        <div class="mt-2 text-sm text-gray-600">Tarik dan lepas file di sini, atau klik untuk memilih</div>
                        <div class="mt-2 text-xs text-gray-400">Dukungan: .xlsx, .xls, .csv</div>
                    </div>
                    <input id="file-input" type="file" name="file" accept=".xlsx,.xls,.csv" required class="absolute inset-0 opacity-0 cursor-pointer" />
                </label>

                @error('file')<div class="text-red-600 text-sm mt-2">{{ $message }}</div>@enderror

                <div id="selected-file" class="mt-3 text-sm text-gray-600"></div>
            </div>

            <div class="mb-4 text-sm text-gray-600">
                Format kolom yang diharapkan (baris pertama header):<br>
                <strong>A:</strong> kode_barang, <strong>B:</strong> nama_barang, <strong>C:</strong> satuan, <strong>D:</strong> keterangan (opsional)
            </div>

            @php
                $importErrors = session('import_errors') ?? [];
            @endphp
            @if(!empty($importErrors))
                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Beberapa baris dilewati</h3>
                    <div class="text-sm text-gray-600 mb-2">Baris yang bermasalah tidak akan diproses. Anda bisa memperbaiki baris tersebut di preview.</div>
                    <div class="rounded border bg-red-50 p-3 text-sm text-red-700">
                        @foreach($importErrors as $err)
                            <div>Baris {{ $err['row'] }}: {{ implode(', ', $err['errors']) }}</div>
                        @endforeach
                    </div>
                </div>
                @php session()->forget('import_errors'); @endphp
            @endif

            <div class="flex items-center space-x-3 mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Preview</button>
                <a href="{{ route('barang.index') }}" class="px-4 py-2 border rounded text-gray-700">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('file-input')?.addEventListener('change', function(e){
    const file = e.target.files[0];
    const el = document.getElementById('selected-file');
    if (!file) { el.textContent = ''; return; }
    el.textContent = file.name + ' (' + Math.round(file.size/1024) + ' KB)';
});
</script>

@endsection
