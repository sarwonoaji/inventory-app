@extends('layouts.app')

@section('title', 'Detail Barang Keluar')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Barang Keluar</h1>
        <p class="text-sm text-gray-500">
            No Transaksi: <span class="font-medium">{{ $barangKeluar->no_transaksi }}</span>
        </p>
        <p class="text-sm text-gray-500">
            Tanggal:
            <span class="font-medium">
                {{ \Carbon\Carbon::parse($barangKeluar->tanggal)->format('d-m-Y') }}
            </span>
        </p>
        @if($barangKeluar->keterangan)
        <p class="text-sm text-gray-500">
            Keterangan: <span class="font-medium">{{ $barangKeluar->keterangan }}</span>
        </p>
        @endif
    </div>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="border px-4 py-3 text-left">Kode Barang</th>
                    <th class="border px-4 py-3 text-left">Nama Barang</th>
                    <th class="border px-4 py-3 text-center">Qty</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($barangKeluar->details as $detail)
                <tr>
                    <td class="border px-4 py-2">
                        {{ $detail->barang->kode_barang }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ $detail->barang->nama_barang }}
                    </td>
                    <td class="border px-4 py-2 text-center font-semibold">
                        {{ $detail->qty }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Total Qty:
                <span class="font-bold">
                    {{ $barangKeluar->details->sum('qty') }}
                </span>
            </div>

            <a href="{{ route('barang-keluar.index') }}"
               class="text-orange-600 hover:text-orange-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>
</div>

@endsection
