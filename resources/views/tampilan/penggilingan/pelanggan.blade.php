@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Data Pelanggan</h1>
</div>
<form class="d-flex kas-search mb-3" role="search">
    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>
<a href="#"> + Tambah Data</a>
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Nomor Telepone</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Nama</td>
            <td>Alamat</td>
            <td>Nomor Telepon</td>
            <td>Status</td>
            <td><a href="#">Edit</a> || <a href="#">Hapus</a></td>
        </tr>
    </tbody>
</table>
@endsection