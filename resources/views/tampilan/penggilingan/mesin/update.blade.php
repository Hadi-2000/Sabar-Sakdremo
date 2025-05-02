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
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('mesin.update', $mesin->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama">Nama Mesin</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{$mesin->nama_mesin}}" required placeholder="Masukan Nama Mesin">
        </div>
        <div class="form-group">
            <label for="merek">Merek Mesin</label>
            <input type="text" class="form-control" id="merek" name="merek" value="{{$mesin->merek_mesin}}" required placeholder="Masukan Merek Mesin">
        </div>
        <div class="form-group mt-3">
            <label for="deskripsi">Deskripsi Mesin</label><br>
            <textarea class="border p-2 form-area w-100" id="deskripsi" name="deskripsi" rows="3" required placeholder="Masukan Deskripsi Mesin">{{$mesin->deskripsi}}</textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
@endsection