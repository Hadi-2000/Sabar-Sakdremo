@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Ubah Data</h1>
</div>
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif
<div>
    <form action="{{ route('aset.update', $produk->id) }}" method="post">
        @csrf
        @method('PUT')
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Produk</label>
            <input type="text" class="form-control" id="nama" name="nama" required value="{{$produk->nama}}" placeholder="Masukan Nama Produk">
        </div>
        <!-- Input Alamat -->
        <div class="mb-3">
            <label for="deskripsi">Deskripsi</label>
            <textarea type="text" class="form-control" id="deskripsi" name="deskripsi" required placeholder="Masukan Alamat">{{$produk->deskripsi}}</textarea>
        </div>
        <div class="mb-3">
            <label for="jumlah">Jumlah Produk</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden"> 
            <input type="text" class="form-control" id="jumlah" name="jumlah" value="{{$produk->jumlah}}" oninput="formatUangInput(this)" placeholder="Masukan jumlah Produk">
        </div>
        <div class="mb-3">
            <label for="satuan">Satuan</label>
            <select class="form-select" id="satuan" name="satuan" required>
                <option value="">Pilih Satuan</option>
                <option value="Kg" {{$produk->satuan == 'Kg' ? 'selected' : ''}}>Kilogram</option>
                <option value="Liter" {{$produk->satuan == 'Liter' ? 'selected' : ''}}>Liter</option>
                <option value="Buah" {{$produk->satuan == 'Buah' ? 'selected' : ''}}>Buah</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="harga_satuan">Harga Satuan</label>
            <input class="form-control" type="text" name="harga_satuan" id="harga_satuan" value="{{$produk->harga_satuan}}" placeholder="Masukan Harga Per Satuan Produk">
        </div>
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div>
    <script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
</div>
@endsection