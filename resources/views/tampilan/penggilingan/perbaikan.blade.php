@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Penggilingan Perbaikan</h1>
</div>
<div class="row">
            <form class="d-flex utang-search mb-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <a href="#"> + Tambah Data</a>
            <table class="table table-bordered bg-light text-dark mt-3">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Mesin</th>
                        <th>Keterangan</th>
                        <th>Jenis Kerusakan</th>
                        <th>Status</th>
                        <th>Tanggal Perbaikan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tanggal</td>
                        <td>nama mesin</td>
                        <td>keterangan</td>
                        <td>kerusakan</td>
                        <td>status</td>
                        <td>tanggal perbaikan</td>
                        <td><a href="#">Selesai</a></a> ||
                            <a href="#">Edit</a> ||
                            <a href="#">Hapus</a>
                        </td>
                    </tr>
                </tbody>
            </table>
</div>
@endsection