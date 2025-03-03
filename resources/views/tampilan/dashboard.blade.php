@extends('layouts.layout1')

@section('container')
    @if(session('error'))
        <script>
            alert("{{ session('berhasil') }}");
        </script>
    @endif

    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/view-chart.js') }}"></script>

    <div class="judul-container">
      <h1>Dashboard</h1>
  </div>
    <!-- /Ringkasan Total Asset -->
    <div class="card mb-3 total-asset">
        <div class="card-body">
          <h5 class="card-title">Total Asset</h5>
          <p class="card-text format" data-saldo="{{ $kasData['totalAsset'][0]['saldo'] ?? 0 }}">
            Rp. {{ number_format($kasData['totalAsset'][0]['saldo'] ?? 0, 0, ',', '.') }}
        </p>
          <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['totalAsset'][0]['updated_at']}}</small></p>
        </div>
      </div>

     <div class="card-group">
      <!-- /.KasOnHand -->
        <div class="card kas-on-hand">
          <div class="card-body">
            <a href="/dashboard/keuangan/kas/search?query=OnHand">
            <h5 class="card-title">Kas On Hand</h5>
            <p class="card-text format" data-saldo="{{ $kasData['OnHand'][0]['saldo'] ?? 0 }}">
              Rp. {{ number_format($kasData['OnHand'][0]['saldo'] ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['OnHand'][0]['updated_at']}}</small></p>
          </a></div>
        </div>

       <!-- /.KasOnOperasional -->
        <div class="card kas-operasional w-30">
            <div class="card-body">
              <a href="/dashboard/keuangan/kas/search?query=Operasional">
              <h5 class="card-title">Kas On Operasional</h5>
              <p class="card-text format" data-saldo="{{ $kasData['Operasional'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['Operasional'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['Operasional'][0]['updated_at']}}</small></p>
            </a></div>
          </div>

       <!-- /.Stock -->
          <div class="card stock w-30">
            <div class="card-body">
              <a href="/dashboard/laporan/stock">
              <h5 class="card-title">Total Stock</h5>
              <p class="card-text format" data-saldo="{{ $kasData['Stock'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['Stock'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['Stock'][0]['updated_at']}}</small></p>
            </a></div>
          </div>
      </div>

      <div class="card-group">
         <!-- /.Stock -->
        <div class="card utang w-30">
          <div class="card-body">
            <a href="/dashboard/keuangan/utang/search?query=Utang">
            <h5 class="card-title">Total Utang</h5>
            <p class="card-text format" data-saldo="{{ $kasData['Utang'][0]['saldo'] ?? 0 }}">
              Rp. {{ number_format($kasData['Utang'][0]['saldo'] ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['Utang'][0]['updated_at']}}</small></p>
          </a></div>
        </div>

      <!-- /.Piutang -->
        <div class="card piutang">
            <div class="card-body">
              <a href="/dashboard/keuangan/utang/search?query=Piutang">
              <h5 class="card-title">Total Piutang</h5>
              <p class="card-text format" data-saldo="{{ $kasData['Piutang'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['Piutang'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['Piutang'][0]['updated_at']}}</small></p>
            </a></div>
          </div>

      <!-- /.pengeluaran -->
          <div class="card pengeluaran">
            <div class="card-body">
              <h5 class="card-title">Total pengeluaran Hari ini</h5>
              <p class="card-text format" data-saldo="{{ $kasData['pengeluara'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['pengeluaran'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['pengeluaran'][0]['updated_at']}}</small></p>
            </div>
          </div>
      </div>
     
      <!-- /.Ringkasan -->
      <div class="card-group">
        <div class="card pendapatan-kotor">
          <div class="card-body">
            <h5 class="card-title">Pendapatan Kotor</h5>
            <p class="card-text format" data-saldo="{{ $kasData['labaKotor'][0]['saldo'] ?? 0 }}">
              Rp. {{ number_format($kasData['labaKotor'][0]['saldo'] ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['labaKotor'][0]['updated_at']}}</small></p>
          </div>
        </div>
        <div class="card pendapatan-bersih">
            <div class="card-body">
              <h5 class="card-title">Pendapatan bersih</h5>
              <p class="card-text format" data-saldo="{{ $kasData['labaBersih'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['labaBersih'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['labaBersih'][0]['updated_at']}}</small></p>
            </div>
          </div>
          <div class="card selisih">
            <div class="card-body">
              <h5 class="card-title">Selisih Keuangan</h5>
              <p class="card-text format" data-saldo="{{ $kasData['selisih'][0]['saldo'] ?? 0 }}">
                Rp. {{ number_format($kasData['selisih'][0]['saldo'] ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kasData['selisih'][0]['updated_at']}}</small></p>
            </div>
          </div>
      </div>

      <label for="chart">Pilih Kategori: </label>
      <select id="chart">
          <option value="total-asset">Total Asset</option>
          <option value="total-on-hand">Total Kas On Hand</option>
          <option value="total-operasional">Total Kas On Operasional</option>
          <option value="total-stock">Total Stock</option>
          <option value="total-utang">Total Utang</option>
          <option value="total-piutang">Total Piutang</option>
          <option value="pengeluaran-hari-ini">Total Pengeluaran Hari ini</option>
          <option value="pendapatan-kotor">Pendapatan Kotor</option>
          <option value="pendapatan-bersih">Pendapatan Bersih</option>
          <option value="selisih-keuangan">Selisih Keuangan</option>
      </select>

      <!-- Input tanggal -->
      <div class="d-flex">
        <p>Dari : </p><input type="date" id="mulai">
        <p> Sampai : </p><input type="date" id="akhir">
      </div>
      
      <button class="btn btn-outline-success" id="loadData">Tampilkan Grafik</button>
      <!-- /.grafik -->
      <div class="grafik">
        <p>Judul Grafik</p>
    
        <div class="chart-container isi-grafik">
            <!-- Dropdown pilihan grafik -->
    
            <!-- Grafik -->
            <canvas id="myChart"></canvas>
        </div>
    </div>
     

@endsection