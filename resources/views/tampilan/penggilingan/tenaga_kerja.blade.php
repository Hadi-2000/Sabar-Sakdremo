@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Penggilingan Tenaga Kerja</h1>
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
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Bagian</th>
                        <th>Gaji</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>nama</td>
                        <td>alamat</td>
                        <td>bagian</td>
                        <td>gaji</td>
                        <td>
                            <a href="#">Masuk</a> ||
                            <a href="#">Tidak Masuk</a>
                        </td>
                        <td>
                            <a href="#">Edit</a> ||
                            <a href="#">Hapus</a>
                        </td>
                    </tr>
                </tbody>
            </table>
</div>
@endsection