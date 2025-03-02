@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Piutang</h1>
</div>
 <!-- Piutang -->
 <div id="piutang" data-bs-parent="#accordion" class="p-3 accordion-item">
    <h2><b>Laporan Piutang</b></h2><br>
    <form class="d-flex utang-search mb-3" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    <a href="#"> + Tambah Data</a>
    <table class="table table-bordered mt-3">
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
                <td><a href="#">Edit</a> || <a href="#">Hapus</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection