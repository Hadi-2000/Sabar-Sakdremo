@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Update Data</h1>
</div>
        
    <div class="ms-5  me-5 p-4 border rounded shadow w-60">
      <form action="{{route('keuangan.kas.update.proses', $arus->id)}}" method="POST">
        @csrf
        @method('put')
        <div class="form-group">
          <label for="keterangan">Keterangan</label>
          <input type="text" class="form-control" id="keterangan" name="keterangan" aria-describedby="keteranganHelp" value="{{$arus->keterangan}}">
          <small id="keteranganHelp" class="form-text text-muted">Masukkan keterangan laporan kas.</small><hr>
        </div>
        <div class="form-group">
            <label for="jenis_kas">Jenis Kas</label><br>>
            <select class="bg-primary" id="jenis_kas" name="jenis_kas" selected="$arus['jenis_kas']">
              <option value="OnHand" {{ $arus->jenis_kas == 'OnHand' ? 'selected' : '' }}>On Hand</option>
              <option value="Operasional" {{ $arus->jenis_kas == 'Operasional' ? 'selected' : '' }}>Operasional</option>
            </select><hr>
          </div>
        <div class="form-group">
          <label for="jenis_transaksi">Jenis Transaksi</label><br>>
          <select class="bg-primary" id="jenis_transaksi" name="jenis_transaksi" selected="$arus_transaksi">
            <option value="Masuk" {{ $arus->jenis_transaksi == 'Masuk' ? 'selected' : '' }}>Masuk</option>
            <option value="Keluar" {{ $arus->jenis_transaksi == 'Keluar' ? 'selected' : '' }}>Keluar</option>
          </select><hr>
        </div>
        <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <input type="hidden" id="jumlah_hidden" name="jumlah_hidden">
            <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan nominal" oninput="formatUangInput(this)" value="{{ $arus->jumlah }}">
          </div><hr><br>
    <div class="modal-footer">
      <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
    </div>

    <script src="{{ asset('js/formatUangInput.js') }}"></script>
@endsection