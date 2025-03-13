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
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('mesin.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="nama_aset">Nama Mesin</label>
            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukan Nama Mesin">
        </div>
        <div class="form-group" mt-3>
            <label for="lokasi_aset">Merek Mesin</label>
            <input type="text" class="form-control" id="merek" name="merek" required placeholder="Masukan Merek Mesin">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
@endsection