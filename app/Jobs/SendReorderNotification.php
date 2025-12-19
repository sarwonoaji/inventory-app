<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Barang;
use App\Mail\ReorderNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendReorderNotification implements ShouldQueue
{
    use Queueable;

    public $barang;

    /**
     * Create a new job instance.
     */
    public function __construct(Barang $barang)
    {
        $this->barang = $barang;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Reorder job triggered for barang: ' . $this->barang->nama_barang);

        // Kirim email reorder ke supplier_email jika ada, atau ke default
        $email = $this->barang->supplier_email ?: 'sarwonoaji27@gmail.com';
        Mail::to($email)->send(new ReorderNotification($this->barang));

        Log::info('Reorder email sent to ' . $email . ' for barang: ' . $this->barang->nama_barang);

        // Jika ada API e-commerce, tambah logic di sini
        // Contoh: Http::post('https://api.ecommerce.com/reorder', ['item' => $this->barang->kode_barang]);
    }
}
