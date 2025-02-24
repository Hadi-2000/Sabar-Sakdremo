@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Profil</h1>
</div>
<div>
    <div class="row">
        <div class="col-md-4">
            <img src="{{ asset('images/user.png') }}" alt="User" class="img-fluid img-thumbnail">
        </div>
        <div class="col-md-8">
            <h3>Nama: John Doe</h3>
            <p>Alamat : </p>
            <p>No. Telp: </p>
            <a href="#" class="btn btn-primary">Edit Profil</a>
            <a href="#" class="btn btn-danger ml-2">Edit Password</a>
        </div>
    </div>
</div>
</div>
@endsection