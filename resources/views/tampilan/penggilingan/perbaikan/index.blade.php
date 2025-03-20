@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Data Perbaikan Mesin</h1>
    </div>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{route('penggilingan.perbaikan.search')}}" method="GET" class="d-flex form-control" role="search">
        <input class="form-control me-2" id="query" name="query" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    <a href="{{ route('perbaikan.create') }}"><button class="btn btn-primary">Tambah Data</button></a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Teknisi</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perbaikan as $p)
                <tr>
                    <td>{{ $p->created_at }}</td>
                    <td>{{ $p->teknisi }}</td>
                    <td>{{ $p->keterangan }}</td>
                    <td>{{ $p->status }}</td>
                    <td>
                        <a href="{{ route('perbaikan.edit', $p->id) }}"><button class="btn btn-warning">Edit</button></a>
                        <form action="{{ route('perbaikan.destroy', $p->id) }}" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if ($perbaikan->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">Data Tidak Ditemukan</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $perbaikan->links() }}
        </div>

@endsection