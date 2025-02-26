@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Kas</h1>
</div>
    <div id="kas" data-bs-parent="#accordion" class="accordion-collapse collapse show note-content bg-light p-3 accordion-item">
        <h2><b>Laporan Kas Masuk dan Keluar</b></h2><br<hr>
        <form action="{{ route('search')}}" method="GET" class="d-flex kas-search mb-3" role="search">
            <input class="form-control me-2" name="query" type="search" placeholder="Search" aria-label="Search" value="{{ request('query')}}">
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
                @if ($arus->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">Data Kosong</td>
                    </tr>
                @endif
                @foreach ($arus as $kas)
                <tr>
                    <td>{{$kas->created_at}}</td>
                    <td>{{$kas->keterangan}}</td>
                    <td>{{$kas->jenis_kas}}</td>
                    <td>{{$kas->jenis_transaksi}}</td>
                    <td>{{number_format($kas->jumlah, 0, ',', '.')}}</td>
                    <td><a href="{{route('update')}}">Edit</a> || <a href="#">Hapus</a></td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>

    <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> 
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{route('create')}}">
            @csrf
            <div class="form-group">
              <label for="keterangan">Keterangan</label>
              <input type="text" class="form-control" id="keterangan" name="keterangan" aria-describedby="keteranganHelp">
              <small id="keteranganHelp" class="form-text text-muted">Masukkan keterangan laporan kas.</small><hr>
            </div>
            <div class="form-group">
                <label for="jenis_kas">Jenis Kas</label><br>>
                <select class="bg-primary" id="jenis_kas" name="jenis_kas"><br>
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
                <input type="number" class="form-control" name="jumlah" id="jumlah" aria-describedby="keteranganHelp" placeholder="Masukan nominal">
              </div><hr>
        <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection