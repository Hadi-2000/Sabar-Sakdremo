@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Tambah Data Utang</h1>
</div>
 <!-- Piutang -->
 <div class="ms-5 me-5 p-4 border rounded shadow w-60">
    <form action="{{ route('keuangan.utang.create.proses') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukan Nama Pelanggan">
        </div>
        <div class="mb-3">
            <label for="alamat_pelanggan" class="form-label">Alamat Pelanggan</label>
            <input type="text" class="form-control" id="alamat_pelanggan" name="alamat_pelanggan" placeholder="Masukan Alamat Pelanggan">
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Total Utang</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden">
            <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Masukan jumlah utang" oninput="formatUangInput(this)">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
@endsection
