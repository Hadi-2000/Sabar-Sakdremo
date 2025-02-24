@extends('layouts.layout1')

@section('container')
    @if(session('error'))
        <script>
            alert("{{ session('berhasil') }}");
        </script>
    @endif
    <div class="judul-container">
      <h1>Dashboard</h1>
  </div>
    <!-- /Ringkasan Total Asset -->
    <div class="card mb-3 total-asset">
        <div class="card-body">
          <h5 class="card-title">Total Asset</h5>
          <p class="card-text">Rp. {{"Total Uang"}}</p>
          <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
        </div>
      </div>

     <!-- /.Ringkasan -->
     <div class="card-group">
        <div class="card kas-on-hand">
          <div class="card-body">
            <h5 class="card-title">Total Kas On Hand</h5>
            <p class="card-text">{{"Rp. Total Uang"}}</p>
            <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
          </div>
        </div>
        <div class="card kas-operasional">
            <div class="card-body">
              <h5 class="card-title">Total Kas On Operasional</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
          <div class="card stock">
            <div class="card-body">
              <h5 class="card-title">Total Stock</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
      </div>

      <!-- /.Ringkasan -->
      <div class="card-group">
        <div class="card utang">
          <div class="card-body">
            <h5 class="card-title">Total Utang</h5>
            <p class="card-text">{{$totalAsset->saldo }}</p>
            <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
          </div>
        </div>
        <div class="card piutang">
            <div class="card-body">
              <h5 class="card-title">Total Piutang</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
          <div class="card pengeluaran">
            <div class="card-body">
              <h5 class="card-title">Total pengeluaran Hari ini</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
      </div>
     
      <!-- /.Ringkasan -->
      <div class="card-group">
        <div class="card pendapatan-kotor">
          <div class="card-body">
            <h5 class="card-title">Pendapatan Kotor</h5>
            <p class="card-text">{{"Rp. Total Uang"}}</p>
            <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
          </div>
        </div>
        <div class="card pendapatan-bersih">
            <div class="card-body">
              <h5 class="card-title">Pendapatan bersih</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
          <div class="card selisih">
            <div class="card-body">
              <h5 class="card-title">Selisih Keuangan</h5>
              <p class="card-text">{{"Rp. Total Uang"}}</p>
              <p class="card-text"><small class="text-body-secondary">{{"last update"}}</small></p>
            </div>
          </div>
      </div>

      <!-- /.grafik -->
      <div class="grafik">
        <p>{{"Judul Grafik"}}</p>
        <div class="chart-container isi-grafik">
          <canvas id="myChart"></canvas>
        </div>
      </div>
     

@endsection