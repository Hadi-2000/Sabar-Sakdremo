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
    <form action="{{ route('tenaga_kerja.store') }}" method="post">
        @csrf
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukan Nama">
        </div>
        <!-- Input Alamat -->
        <div class="mb-3">
            <label for="alamat">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" required placeholder="Masukan Alamat">
        </div>
        <div class="mb-3">
            <label for="alamat">Nomor Telepon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="Masukan Nomor Telepon">
        </div>
        <div class="mb-3">
            <label for="alamat">Gaji</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden"> 
            <input type="text" class="form-control" id="jumlah" name="jumlah" oninput="formatUangInput(this)" placeholder="Masukan jumlah gaji">
        </div>
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div>
    <script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
</div>
@endsection