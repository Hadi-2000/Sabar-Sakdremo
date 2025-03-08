@extends('layouts.layout1')

@section('container')
    <div class="judul-container">Tambah Pelanggan</div>
    <div>
        <form action="{{ route('penggilingan.pelanggan.update.proses', $pelanggan->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required value="{{$pelanggan->nama}}" placeholder="Masukan nama ">
            </div>
            <div class="mb-3">
                <label for="no_telepon">Telepon</label>
                <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{$pelanggan->no_telepon}}" placeholder="Masukan Nomor Telepon">
            </div>
            <div class="mb-3">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" placeholder="Masukan Alamat">{{$pelanggan->alamat}}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

@endsection