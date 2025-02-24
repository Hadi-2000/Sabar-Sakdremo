@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Stock & Produk</h1>
</div>

<form class="d-flex kas-search mb-3" role="search">
    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>
<a href="#"> + Tambah Data</a>
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Harga per kg</th>
            <th>Stock per kg</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>tanggal</td>
            <td>nama barang</td>
            <td>harga per kg</td>
            <td>stock per kg</td>
            <td><a href="#">Edit</a> || <a href="#">Hapus</a></td>
        </tr>
    </tbody>
</table>
@endsection