@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Profil</h1>
</div>
<div>
    <div class="row">
        <div class="col-md-4">
            <img src="{{ asset('storage/images/profile/' . Auth::user()->foto_user)}}" alt="User" height="200px" width="200px">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered table-hover table-responsive">
                <tr>
                    <th>Username</th>
                    <td>{{Auth::user()->username}}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{Auth::user()->nama}}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{Auth::user()->alamat}}</td>
                </tr>
                <tr>
                    <th>No Handphone</th>
                    <td>{{Auth::user()->no_hp}}</td>
                </tr>
            </table>
            <a href="{{route('profile.edit.data')}}" class="btn btn-primary">Edit Profil</a>
            <a href="{{route('profile.edit.password')}}" class="btn btn-danger ml-2">Edit Password</a>
        </div>
    </div>
</div>
</div>
@endsection