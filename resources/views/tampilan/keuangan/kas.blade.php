@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Kas</h1>
</div>
    <div id="kas" data-bs-parent="#accordion" class="accordion-collapse collapse show note-content bg-light p-3 accordion-item">
        <h2><b>Laporan Kas Masuk dan Keluar</b></h2><br<hr>
        <select id="kas-general" class="form-select mb-2">
            <option>Kas On Hand</option>
            <option>Kas Operasional</option>
        </select>
        <select id="kas-filter" class="form-select mb-2">
            <option>Kas Masuk</option>
            <option>Kas Keluar</option>
        </select>
        <form class="d-flex kas-search mb-3" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="#"> + Tambah Data</a>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>tanggal</td>
                    <td>keterangan</td>
                    <td>jenis</td>
                    <td>jumlah</td>
                    <td><a href="#">Edit</a> || <a href="#">Hapus</a></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection