<?php

namespace App\Jobs;

use App\Models\kas;
use App\Models\ArusKas;
use App\Models\Pegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateKasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tgl = date('Y-m-d');

        $kasData = kas::whereIn('jenis_kas', [
            'totalAsset', 'OnHand', 'Operasional', 'Stock',
            'Utang', 'Piutang', 'labaBersih', 'labaKotor',
            'pengeluaran', 'selisih', 'pemasukan'
        ])->get()->keyBy('jenis_kas');

        // Hitung kembali total asset
        $totalAsset = $kasData['OnHand']->saldo
            + $kasData['Operasional']->saldo
            + $kasData['Piutang']->saldo
            + $kasData['Stock']->saldo
            - $kasData['Utang']->saldo;

        if ($kasData['totalAsset']->saldo != $totalAsset) {
            $kasData['totalAsset']->update(['saldo' => $totalAsset]);
        }

        // Pemasukan & Pengeluaran
        $pemasukan = ArusKas::whereDate('updated_at', $tgl)
            ->where('jenis_transaksi', 'Masuk')
            ->sum('jumlah');

        $pengeluaran = ArusKas::whereDate('updated_at', $tgl)
            ->where('jenis_transaksi', 'Keluar')
            ->sum('jumlah');

        if ($kasData['pemasukan']->saldo != $pemasukan) {
            $kasData['pemasukan']->update(['saldo' => $pemasukan]);
        }

        if ($kasData['pengeluaran']->saldo != $pengeluaran) {
            $kasData['pengeluaran']->update(['saldo' => $pengeluaran]);
        }

        // Laba kotor dan bersih
        $labaKotor = $pemasukan - $pengeluaran;
        if ($kasData['labaKotor']->saldo != $labaKotor) {
            $kasData['labaKotor']->update(['saldo' => $labaKotor]);
        }

        $pegawaiGajiHariIni = Pegawai::where('kehadiran', 'Pulang')
            ->whereDate('updated_at', $tgl)
            ->sum('gaji_hari_ini');

        $labaBersih = $labaKotor - $pegawaiGajiHariIni;

        if ($kasData['labaBersih']->saldo != $labaBersih) {
            $kasData['labaBersih']->update(['saldo' => $labaBersih]);
        }

        // Selisih
        $selisih = ArusKas::whereDate('updated_at', $tgl)
            ->where('keterangan', 'gap')
            ->sum('jumlah');

        if ($kasData['selisih']->saldo != $selisih) {
            $kasData['selisih']->update(['saldo' => $selisih]);
        }

        // Simpan timestamp terakhir update ke cache
        Cache::put('last_kas_update', now(), now()->addMinutes(5));
    }
}
