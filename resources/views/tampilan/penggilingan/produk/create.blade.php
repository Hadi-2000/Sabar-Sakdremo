@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Tambah Data</h1>
</div>
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif
<div>
    <form action="{{ route('aset.store') }}" method="post">
        @csrf
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Produk</label>
            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukan Nama Produk">
        </div>
        <!-- Input Alamat -->
        <div class="mb-3">
            <label for="deskripsi">Deskripsi</label>
            <textarea type="text" class="form-control" id="deskripsi" name="deskripsi" required placeholder="Masukan Alamat"></textarea>
        </div>
        <div class="mb-3">
            <label for="jumlah">Jumlah Produk</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden"> 
            <input type="text" class="form-control" id="jumlah" name="jumlah" oninput="formatUangInput(this)" placeholder="Masukan jumlah Produk">
        </div>
        <div class="mb-3">
            <label for="satuan">Satuan</label>
            <select class="form-select" id="satuan" name="satuan" required>
                <option value="">Pilih Satuan</option>
                <option value="Kg">Kilogram</option>
                <option value="Liter">Liter</option>
                <option value="Buah">Buah</option>
            </select>
        </div>
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div>
    <script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
</div>
@endsection