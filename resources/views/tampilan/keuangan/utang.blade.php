@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Utang</h1>
</div>
<!-- Utang -->
<div id="utang" data-bs-parent="#accordion" class="p-3 accordion-item">
    <form action="{{ route('keuangan.utang.search') }}" method="GET" class="d-flex utang-search mb-3" role="search">
        <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    <a href="/dashboard/keuangan/utang/create"> + Tambah Data</a>
    <table class="table table-bordered bg-light text-dark mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal Perubahan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if(session('error'))
                <tr class="alert alert-danger text-center">
                    <td colspan="7" class="text-center text-danger">{{ session('error') }}</td>
                </tr>
            @endif
            @if(!$utang->isEmpty())
                @foreach ($utang as $item)
                    <tr>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->nama}}</td>
                        <td>{{$item->alamat}}</td>
                        <td>{{$item->nominal}}</td>
                        <td>{{$item->status}}</td>
                        <td>{{$item->upadated_at}}</td>
                        <td><a href="#">Lunas</a> ||
                            <a href="#">Nyicil</a> ||
                            <a href="#">Edit</a> ||
                            <a href="#">Hapus</a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection