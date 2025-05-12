@extends('layouts.layout1')

@section('container')
    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif

    <script src="{{ asset('js/dashboard.js') }}"></script>
    

    <div class="judul-container">
      <h1>Dashboard</h1>
  </div>
    <!-- /Ringkasan Total Asset -->
    <div class="card mb-3 total-asset">
        <div class="card-body">
          <h5 class="card-title">Total Asset</h5>
          <p class="card-text format text-dark" data-saldo="{{ $kas2['totalAsset']->saldo ?? 0 }}">
            Rp. {{ number_format($kas2['totalAsset']->saldo ?? 0, 0, ',', '.') }}
        </p>
          <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['totalAsset']->updated_at}}</small></p>
        </div>
      </div>

     <div class="card-group">
      <!-- /.KasOnHand -->
        <div class="card kas-on-hand">
          <div class="card-body">
            <a href="/dashboard/keuangan/kas/search?query=OnHand">
            <h5 class="card-title">Kas On Hand</h5>
            <p class="card-text format" data-saldo="{{ $kas2['OnHand']->saldo ?? 0 }}">
              Rp. {{ number_format($kas2['OnHand']->saldo ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['OnHand']->updated_at}}</small></p>
          </a></div>
        </div>

       <!-- /.KasOnOperasional -->
        <div class="card kas-operasional w-30">
            <div class="card-body">
              <a href="/dashboard/keuangan/kas/search?query=Operasional">
              <h5 class="card-title">Kas On Operasional</h5>
              <p class="card-text format" data-saldo="{{ $kas2['Operasional']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['Operasional']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['Operasional']->updated_at}}</small></p>
            </a></div>
          </div>

       <!-- /.Stock -->
          <div class="card stock w-30">
            <div class="card-body">
              <a href="{{route('stock.index')}}">
              <h5 class="card-title">Total Stock</h5>
              <p class="card-text format" data-saldo="{{ $kas2['Stock']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['Stock']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['Stock']->updated_at}}</small></p>
            </a></div>
          </div>
      </div>

      <div class="card-group">
         <!-- /.Stock -->
        <div class="card utang w-30">
          <div class="card-body">
            <a href="{{route('utang.index')}}">
            <h5 class="card-title">Total Utang</h5>
            <p class="card-text format" data-saldo="{{ $kas2['Utang']->saldo ?? 0 }}">
              Rp. {{ number_format($kas2['Utang']->saldo ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['Utang']->updated_at}}</small></p>
          </a></div>
        </div>

      <!-- /.Piutang -->
        <div class="card piutang">
            <div class="card-body">
              <a href="{{route('piutang.index')}}">
              <h5 class="card-title">Total Piutang</h5>
              <p class="card-text format" data-saldo="{{ $kas2['Piutang']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['Piutang']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['Piutang']->updated_at}}</small></p>
            </a></div>
          </div>

      <!-- /.pengeluaran -->
          <div class="card pengeluaran">
            <div class="card-body">
              <h5 class="card-title">Total pengeluaran Hari ini</h5>
              <p class="card-text format" data-saldo="{{ $kas2['pengeluaran']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['pengeluaran']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['pengeluaran']->updated_at}}</small></p>
            </div>
          </div>
      </div>
     
      <!-- /.Ringkasan -->
      <div class="card-group">
        <div class="card pendapatan-kotor">
          <div class="card-body">
            <h5 class="card-title">Pendapatan Kotor</h5>
            <p class="card-text format" data-saldo="{{ $kas2['labaKotor']->saldo ?? 0 }}">
              Rp. {{ number_format($kas2['labaKotor']->saldo ?? 0, 0, ',', '.') }}
          </p>
            <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['labaKotor']->updated_at}}</small></p>
          </div>
        </div>
        <div class="card pendapatan-bersih">
            <div class="card-body">
              <h5 class="card-title">Pendapatan bersih</h5>
              <p class="card-text format" data-saldo="{{ $kas2['labaBersih']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['labaBersih']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['labaBersih']->updated_at}}</small></p>
            </div>
          </div>
          <div class="card selisih">
            <div class="card-body">
              <h5 class="card-title">Selisih Keuangan</h5>
              <p class="card-text format" data-saldo="{{ $kas2['selisih']->saldo ?? 0 }}">
                Rp. {{ number_format($kas2['selisih']->saldo ?? 0, 0, ',', '.') }}
            </p>
              <p class="card-text"><small class="text-body-secondary">{{"last update ".$kas2['selisih']->updated_at}}</small></p>
            </div>
          </div>
      </div>

@endsection