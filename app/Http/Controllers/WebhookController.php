<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        // Cek status order dari Berdu
        if (isset($data['status']) && $data['status'] === 'paid') {
            $phone = $data['customer']['phone'] ?? null;
            $name = $data['customer']['name'] ?? 'Customer';
            $orderId = $data['order_id'] ?? '-';

            if ($phone) {
                // Format pesan
                $message = "Halo {$name}, pembayaran untuk Order #{$orderId} sudah BERHASIL ✅. Terima kasih 🙏";

                // Kirim ke Fonnte
                Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->post('https://api.fonnte.com/send', [
                    'target' => $phone,
                    'message' => $message,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
