<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;

class GenerateAlpha extends Command
{
    protected $signature = 'absensi:generate-alpha';
    protected $description = 'Generate Alpha otomatis untuk pegawai yang tidak absen di hari kerja';

    public function handle()
    {
        $today = Carbon::now('Asia/Jakarta');
        $dayName = $today->format('l');

        if (in_array($dayName, ['Saturday', 'Sunday'])) {
            $this->info("Hari libur, tidak perlu mencatat Alpha.");
            return;
        }

        $pegawaiList = User::where('role', 'pegawai')->get();
        $generated = [];

        foreach ($pegawaiList as $pegawai) {
            $sudahAbsensi = Absensi::where('pegawai_id', $pegawai->id)
                                   ->whereDate('tanggal', $today->toDateString())
                                   ->exists();

            if (!$sudahAbsensi) {
                Absensi::create([
                    'pegawai_id' => $pegawai->id,
                    'tanggal' => $today->toDateString(),
                    'status' => 'Alpha',
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                    'lokasi_masuk' => null,
                    'lokasi_keluar' => null,
                ]);
                $generated[] = $pegawai->id;
            }
        }

        $this->info("Berhasil mencatat Alpha untuk " . count($generated) . " pegawai.");
    }
}
