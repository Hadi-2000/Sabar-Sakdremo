try {
            DB::beginTransaction();
    
            $tgl = date('Y-m-d');
    
            $kas2 = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'
            ])->get()->keyBy('jenis_kas');

            if($kas2['totalAsset']->updated_at->format('Y-m-d') != $tgl){
                $kas2['totalAsset']->update(['saldo_lama' => $kas2['totalAsset']->saldo]);
            }
           
            $totalAsset = $kas2['OnHand']->saldo + 
            $kas2['Operasional']->saldo + 
            $kas2['Piutang']->saldo + 
            $kas2['stock']->saldo - 
            $kas2['Utang']->saldo;
    
            if ($kas2['totalAsset']->saldo != $totalAsset) {
                $kas2['totalAsset']->update(['saldo' => $totalAsset]);
            }
            //tambahan
            $pemasukan = ArusKas::whereDate('updated_at', $tgl)
            ->where('jenis_transaksi','Masuk')
            ->sum('jumlah'); 

            if($kas2['pemasukan']->saldo != $pemasukan){
                $kas2['pemasukan']->update(['saldo' => $pemasukan]);
            }
          
            $pengeluaran = ArusKas::whereDate('updated_at', $tgl)
            ->where('jenis_transaksi','Keluar')
            ->sum('jumlah');
            if($kas2['pengeluaran']->saldo != $pengeluaran){
                $kas2['pengeluaran']->update(['saldo' => $pengeluaran]);
            }
    
        $selisih = ArusKas::whereDate('updated_at', $tgl)
            ->where('keterangan','gap')->get();

        $selisih2 = 0;
        foreach($selisih as $s){
             $selisih2 += ($s->jenis_transaksi == 'Masuk') ? $s->jumlah : -$s->jumlah;
        }

        if($selisih2 != $kas2['selisih']->saldo){
            $kas2['selisih']->update(['saldo' => $selisih2]);
        }

        $beban_gaji_fix = 0;
        $pegawaiList = Pegawai::where('kehadiran','Pulang')
        ->whereDate('updated_at', $tgl)
        ->get();

        foreach ($pegawaiList as $p) {
            if ($p->updated_at->format('Y-m-d') !== $tgl) {
                if ($p->kehadiran === 'Pulang' && $p->cek_in && $p->cek_out) {
                    $beban_gaji = 0;

                    if ($p->cek_in >= '08:00:00' && $p->cek_in <= '09:00:00') {
                        if ($p->cek_out >= '11:30:00' && $p->cek_out <= '13:00:00') {
                            $beban_gaji = $p->gaji / 2;
                        } elseif ($p->cek_out >= '15:00:00' && $p->cek_out <= '18:00:00') {
                            $beban_gaji = $p->gaji;
                        }
                    } elseif ($p->cek_in >= '11:30:00' && $p->cek_in <= '13:00:00') {
                        if ($p->cek_out >= '15:00:00' && $p->cek_out <= '18:00:00') {
                            $beban_gaji = $p->gaji / 2;
                        }
                    }
                    $beban_gaji_fix += $beban_gaji;
                }
            }
        }
        if($kas2['labaKotor']->updated_at != $tgl){
            $kas2['labaKotor']->update(['saldo' => $pemasukan - $pengeluaran]);
        }
        if($kas2['labaBersih']->updated_at != $tgl){
            $kas2['labaBersih']->update(['saldo' => $pemasukan - $pengeluaran - $beban_gaji_fix]);
        }
    
            DB::commit();
            return view('tampilan.dashboard', compact('kas2'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada DashboardController@update: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan pada kode.');
        } catch (\PDOException $e) {
            DB::rollBack();
            Log::error('Error pada DashboardController@update: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan pada database');
        }
