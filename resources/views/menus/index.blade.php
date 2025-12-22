@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Tambah Menu</h2>
                    <a href="{{ route('menus.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambah Menu
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Nama</th>
                            <th class="py-2 px-4 border-b">Route</th>
                            <th class="py-2 px-4 border-b">Roles</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $menu->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $menu->route }}</td>
                            <td class="py-2 px-4 border-b">{{ $menu->getRoles()->implode(', ') }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('menus.edit', $menu) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="POST" action="{{ route('menus.destroy', $menu) }}" class="inline ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection