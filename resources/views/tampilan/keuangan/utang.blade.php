@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Utang</h1>
</div>
<!-- Utang -->
<div id="utang" data-bs-parent="#accordion" class="p-3 accordion-item">
    <form class="d-flex utang-search mb-3" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    <a href="#"> + Tambah Data</a>
    <table class="table table-bordered bg-light text-dark mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal Pelunasan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>tanggal</td>
                <td>nama</td>
                <td>alamat</td>
                <td>jumlah</td>
                <td>status</td>
                <td>tanggal pelunasan</td>
                <td><a href="#">Lunas</a></a> ||
                    <a href="#">Edit</a> ||
                    <a href="#">Hapus</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection