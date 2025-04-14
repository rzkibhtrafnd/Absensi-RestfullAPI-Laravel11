<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\QrCodeToken;
use Carbon\Carbon;

class GenerateQrCodeDaily extends Command
{
    protected $signature = 'qr:generate-daily';
    protected $description = 'Generate QR Code otomatis satu kali per hari';

    public function handle()
    {
        $now = now('Asia/Jakarta');
        $hour = (int) $now->format('H');
        $this->info("⏰ Sekarang jam: " . $now->format('Y-m-d H:i:s'));
    
        $type = null;
    
        if ($hour >= 6 && $hour <= 11) {
            $type = 'checkin';
        } elseif ($hour >= 14 && $hour <= 21) {
            $type = 'checkout';
        } else {
            $this->info("⏳ Di luar waktu generate QR Code (pagi/sore)");
            return;
        }
    
        // Cek apakah QR untuk tipe ini sudah dibuat hari ini
        $alreadyGenerated = QrCodeToken::whereDate('created_at', $now->toDateString())
            ->where('type', $type)
            ->exists();
    
        if ($alreadyGenerated) {
            $this->info("⚠️ QR Code untuk $type sudah digenerate hari ini.");
            return;
        }
    
        if ($type === 'checkin') {
            $expiredAt = $now->copy()->setTime(11, 0, 0);
        } elseif ($type === 'checkout') {
            $expiredAt = $now->copy()->setTime(21, 0, 0);
        }
    
        $token = Str::random(32);
    
        QrCodeToken::create([
            'token' => $token,
            'expired_at' => $expiredAt,
            'type' => $type,
            'generated_by' => null
        ]);
    
        $this->info("✅ QR Code $type berhasil digenerate pada {$now->format('Y-m-d H:i:s')} dan expired pada {$expiredAt->format('H:i')}");
    }
    
    
}
