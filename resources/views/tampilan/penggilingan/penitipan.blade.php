@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Penggilingan Penitipan</h1>
</div>
< class="row">
    <!-- Sidebar Menu dengan Dropdown -->
    <!-- Konten Note -->
    <div class="col-md-9" id="accordion">
        <!-- penitipan -->
            <form class="d-flex kas-search mb-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <a href="#"> + Tambah Data</a>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Barang</th>
                        <th>Jumlah</th> <!--Tambah database untuk menyimpan perubahan-->
                        <th>Riwayat Penitipan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>tanggal</td>
                        <td>nama</td>
                        <td>barang</td>
                        <td>jumlah</td>
                        <td><a href="#">Riwayat</a></td>
                        <td><a href="#">Edit</a> || <a href="#">Hapus</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
</div>
@endsection