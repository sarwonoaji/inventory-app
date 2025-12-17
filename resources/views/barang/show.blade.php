@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Detail Barang</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-xs text-gray-500">Kode</div>
            <div class="font-medium text-gray-800">{{ $barang->kode_barang }}</div>
        </div>

        <div>
            <div class="text-xs text-gray-500">Nama</div>
            <div class="font-medium text-gray-800">{{ $barang->nama_barang }}</div>
        </div>

        <div>
            <div class="text-xs text-gray-500">Satuan</div>
            <div class="font-medium text-gray-800">{{ $barang->satuan }}</div>
        </div>

        <div>
            <div class="text-xs text-gray-500">Stok</div>
            <div class="font-medium text-gray-800">{{ $barang->stok }}</div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('barang.index') }}" class="inline-block bg-gray-100 px-4 py-2 rounded-md">Kembali</a>
        <a href="{{ route('barang.edit', $barang->id) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md ml-2">Edit</a>
    </div>
</div>

@endsection
