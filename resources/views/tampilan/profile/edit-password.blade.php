@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Mengubah Data Profile</h1>
    </div>
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{route('profile.update.password')}}" method="post">
        @csrf
        <div class="mb-3">
            <label for="foto_user">Preview Foto User</label>
            <img id="preview" src="{{ asset('storage/images/profile/' . Auth::user()->foto_user)}}" alt="User" height="100px" width="100px">
            <input type="file" class="form-control mt-2" id="foto_user" name="foto_user" onchange="previewImage()">
            <small id="fileHelp" class="form-text text-muted">Ukuran maksimal 2MB. Format gambar JPG/JPEG/PNG.</small>
        </div>
        <!-- data user -->
        <div class="mb-3">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ Auth::user()->nama }}">
        </div>
        <div class="mb-3">
            <label for="alamat">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" value="{{ Auth::user()->alamat }}">
        </div>
        <div class="mb-3">
            <label for="no_hp">Nomor Telepon</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ Auth::user()->no_hp }}">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
    <script src="{{asset('js/previewImage.js')}}"></script>
@endsection