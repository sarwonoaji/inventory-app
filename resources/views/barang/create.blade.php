@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')

<div class="max-w-2xl bg-white p-6 rounded-xl shadow border border-gray-200">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Barang</h1>
        <p class="text-sm text-gray-500">Input data master barang</p>
    </div>

    {{-- ERROR VALIDATION --}}
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('barang.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- KODE BARANG -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kode Barang
            </label>
            <input type="text"
                   name="kode_barang"
                   value="{{ old('kode_barang') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="BRG-001">
        </div>

        <!-- NAMA BARANG -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Barang
            </label>
            <input type="text"
                   name="nama_barang"
                   value="{{ old('nama_barang') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="Kertas A4">
        </div>

        <!-- SATUAN -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Satuan
            </label>
            <input type="text"
                   name="satuan"
                   value="{{ old('satuan') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="pcs / box / rim">
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Keterangan
            </label>
            <textarea name="keterangan"
                      rows="3"
                      class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Catatan tambahan">{{ old('keterangan') }}</textarea>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('barang.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Batal
            </a>

            <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                Simpan
            </button>
        </div>

    </form>

</div>

@endsection
