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
            <label for="password_lama">Password Lama</label>
            <input type="text" class="form-control" id="password_lama" name="password_lama" required placeholder="Masukan Password Lama">
        </div>
        <div class="mb-3">
            <label for="password_baru">Password Baru</label>
            <input type="password" class="form-control" id="password_baru" name="password_baru" required placeholder="Masukan Password Baru">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
@endsection