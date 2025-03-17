@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Tambah Data</h1>
</div>
        
    <div class="ms-5  me-5 p-4 border rounded shadow w-60">
      <form action="{{route('kas.store')}}" method="POST">
        @csrf
        <div class="form-group">
          <label for="keterangan">Keterangan</label>
          <input type="text" class="form-control" id="keterangan" name="keterangan" aria-describedby="keteranganHelp">
          <small id="keteranganHelp" class="form-text text-muted">Masukkan keterangan laporan kas.</small><hr>
        </div>
        <div class="form-group">
            <label for="jenis_kas">Jenis Kas</label><br>>
            <select class="bg-primary" id="jenis_kas" name="jenis_kas">
              <option value="OnHand">On Hand</option>
              <option value="Operasional">Operasional</option>
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
            <input type="hidden" id="jumlah_hidden" name="jumlah_hidden">
            <input type="text" class="form-control" id="jumlah" placeholder="Masukkan nominal" oninput="formatUangInput(this)">
          </div><hr><br>
    <div class="modal-footer">
      <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
    </div>

    <script src="{{ asset('js/formatUangInput.js') }}"></script>
@endsection