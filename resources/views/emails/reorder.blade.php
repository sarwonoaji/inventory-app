{{-- resources/views/emails/reorder.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Reorder Notification</title>
</head>
<body>
    <h1>Reorder Notification</h1>
    <p>Barang <strong>{{ $barang->nama_barang }}</strong> (Kode: {{ $barang->kode_barang }}) telah mencapai batas minimum stok.</p>
    <p>Stok saat ini: {{ $barang->stok }}</p>
    <p>Batas minimum: {{ $barang->min_stok }}</p>
    <p>Silakan lakukan reorder segera.</p>
    <p>Terima kasih.</p>
</body>
</html>