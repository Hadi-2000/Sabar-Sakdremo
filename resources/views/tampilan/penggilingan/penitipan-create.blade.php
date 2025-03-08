@extends('layouts.layout1')

@section('container')
    <div class="judul-container">Tambah Penitipan</div>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div>
        <form action="{{ route('penggilingan.pelanggan.create.proses') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukan nama ">
            </div>
            <div class="mb-3">
                <label for="no_telepon">Telepon</label>
                <input type="text" class="form-control" id="no_telepon" name="no_telepon" placeholder="Masukan Nomor Telepon">
            </div>
            <div class="mb-3">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" placeholder="Masukan Alamat"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>

@endsection