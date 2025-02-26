@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Kas</h1>
</div>
    <div id="kas" data-bs-parent="#accordion" class="accordion-collapse collapse show note-content bg-light p-3 accordion-item">
        <h2><b>Laporan Kas Masuk dan Keluar</b></h2><br<hr>
        <form action="{{ route('keuangan.kas.search')}}" method="GET" class="d-flex kas-search mb-3" role="search">
            <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search" value="{{ request('query')}}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        + Tambah Data
      </button>
      <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jenis Kas</th>
                <th>Jenis Transaksi</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (session('error'))
                <tr class="alert alert-danger text-center">
                    <td colspan="6" class="text-center text-danger">{{ session('error') }}</td>
                </tr>
            @endif
    
            @if (!$arus->isEmpty())
                @foreach ($arus as $kas)
                    <tr>
                        <td>{{ $kas->created_at }}</td>
                        <td>{{ $kas->keterangan }}</td>
                        <td>{{ $kas->jenis_kas }}</td>
                        <td>{{ $kas->jenis_transaksi }}</td>
                        <td>{{ number_format($kas->jumlah, 0, ',', '.') }}</td>
                        <td>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#update{{ $kas->id }}">Edit</button> |
                            <a href="#">Hapus</a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">Data Kosong</td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>

    <!-- Modal Create -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{route('keuangan.kas.create')}}" method="POST">
            @csrf
            <div class="form-group">
              <label for="keterangan">Keterangan</label>
              <input type="text" class="form-control" id="keterangan" name="keterangan" aria-describedby="keteranganHelp">
              <small id="keteranganHelp" class="form-text text-muted">Masukkan keterangan laporan kas.</small><hr>
            </div>
            <div class="form-group">
                <label for="jenis_kas">Jenis Kas</label><br>>
                <select class="bg-primary" id="jenis_kas" name="jenis_kas">
                  <option value="totalOnHand">On Hand</option>
                  <option value="totalOperasional">Operasional</option>
                </select><hr>
              </div>
            <div class="form-group">
              <label for="jenis_transaksi">Jenis Transaksi</label><br>>
              <select class="bg-primary" id="jenis_transaksi" name="jenis_transaksi">
                <option value="Masuk">Masuk</option>
                <option value="Keluar">Keluar</option>
              </select><hr>
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="text" class="form-control" name="jumlah" id="jumlah" aria-describedby="keteranganHelp" placeholder="Masukan nominal" oninput="formatUangInput(this)" onkeydown="handleBackspace(event, this)">
              </div><hr>
        <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>

     <!-- Modal Update -->
<div class="modal fade" id="update{{$kas->id}}" tabindex="-1" aria-labelledby="exampleModalUpdate" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalUpdate">Edit Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('keuangan.kas.update')}}">
          @csrf
          <div class="form-group">
            <label for="keteranganUpdate">Keterangan</label>
            <input type="text" class="form-control" id="keteranganUpdate" name="keteranganUpdate" aria-describedby="keteranganHelp">
            <small id="keteranganHelp" class="form-text text-muted">Masukkan keterangan laporan kas.</small><hr>
          </div>
          <div class="form-group">
              <label for="jenisKasUpdate">Jenis Kas</label><br>>
              <select class="bg-primary" id="jenisKasUpdate" name="jenisKasUpdate">
                <option value="totalOnHand">On Hand</option>
                <option value="totalOperasional">Operasional</option>
              </select><hr>
            </div>
          <div class="form-group">
            <label for="jenisTransaksiUpdate">Jenis Transaksi</label><br>>
            <select class="bg-primary" id="jenisTransaksiUpdate" name="jenisTransaksiUpdate">
              <option value="Masuk">Masuk</option>
              <option value="Keluar">Keluar</option>
            </select><hr>
          </div>
          <div class="form-group">
              <label for="jumlahUpdate">Jumlah</label>
              <input type="number" class="form-control" name="jumlahUpdate" id="jumlahUpdate" aria-describedby="keteranganHelp" placeholder="Masukan nominal" oninput="formatUangInput(this)">
            </div><hr>
      <div class="modal-footer">
        <button type="submit" name="submitUpdate" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
    <script src="{{ asset('js/formatUangInput.js') }}"></script>
@endsection