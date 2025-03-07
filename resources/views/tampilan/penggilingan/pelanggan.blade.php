@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Data Pelanggan</h1>
</div>
<form action="{{route('penggilingan.pelanggan.search')}}" method="get" class="d-flex kas-search mb-3" role="search">
    <input class="form-control me-2" id="query" name="query" type="search" placeholder="Search" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>
<a href="{{route('penggilingan.pelanggan.create')}}"> + Tambah Data</a>
@if(session('success'))
        <p class="alert alert text-center">
            {{ session('success') }}
        </tr>
     @endif
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if (session('error'))
            <tr>
                <td colspan="4" class="text-center text-danger">{{ session('error') }}</td>
            </tr>
        @endif
        @if (!empty($pelanggan))
            @foreach ($pelanggan as $p)
            <tr>
                <td>{{$p->nama}}</td>
                <td>{{$p->alamat}}</td>
                <td>{{$p->no_telepon}}</td>
                <td><a href="{{route('penggilingan.pelanggan.update', $p->id)}}">
                        <button class="btn btn-primary">Edit</button>
                    </a> |
                    | <form method="POST" action="{{route('penggilingan.pelanggan.destroy', $p->id)}}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form></td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" class="text-center">Data kosong</td>
            </tr>
        @endif
    </tbody>
</table>
<div class=" d-flex justify-content-center">
    {{ $pelanggan->links() }}
</div>
@endsection