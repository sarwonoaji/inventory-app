@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')

<div class="max-w-2xl bg-white p-6 rounded-xl shadow border border-gray-200 mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Scan QR Code Barang</h1>
        <p class="text-sm text-gray-500">Scan QR code untuk melihat detail barang</p>
    </div>

    <div class="space-y-4">
        <div>
            <video id="video" width="100%" height="300" class="border rounded-lg"></video>
        </div>

        <div id="result" class="p-4 bg-gray-100 rounded-lg hidden">
            <h3 class="font-semibold">Detail Barang:</h3>
            <p><strong>Nama:</strong> <span id="nama_barang"></span></p>
            <p><strong>Kode:</strong> <span id="kode_barang"></span></p>
            <p><strong>Stok:</strong> <span id="stok"></span></p>
            <p><strong>Satuan:</strong> <span id="satuan"></span></p>
            <p><strong>Min Stok:</strong> <span id="min_stok"></span></p>
            <p><strong>Keterangan:</strong> <span id="keterangan"></span></p>
        </div>

        <button id="start-scan" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Mulai Scan
        </button>
    </div>

</div>

<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const resultDiv = document.getElementById('result');
    const startButton = document.getElementById('start-scan');
    let scanner;

    startButton.addEventListener('click', function() {
        if (!scanner) {
            scanner = new Instascan.Scanner({ video: video });
            scanner.addListener('scan', function (content) {
                console.log(content);
                processQrCode(content);
                scanner.stop();
            });
        }

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                // Prefer back camera
                const backCamera = cameras.find(c => c.name.toLowerCase().includes('back') || c.name.toLowerCase().includes('rear') || c.name.toLowerCase().includes('environment'));
                scanner.start(backCamera || cameras[0]);
            } else {
                alert('No cameras found.');
            }
        }).catch(function (e) {
            alert('Camera access denied or not supported.');
            console.error(e);
        });
    });

    function processQrCode(qrCode) {
        fetch('{{ route("barang.process-qr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: qrCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById('nama_barang').textContent = data.nama_barang;
                document.getElementById('kode_barang').textContent = data.kode_barang;
                document.getElementById('stok').textContent = data.stok;
                document.getElementById('satuan').textContent = data.satuan;
                document.getElementById('min_stok').textContent = data.min_stok;
                document.getElementById('keterangan').textContent = data.keterangan;
                resultDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses QR code');
        });
    }
});
</script>

@endsection