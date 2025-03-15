@extends('layouts.layout1')

@section('container')

    <div class="judul-container">
        <h1>Data Mesin</h1>
    </div>
    <form action="{{route('penggilingan.mesin.search')}}" method="get" class="d-flex kas-search mb-3" role="search">
        <input class="form-control me-2" id="query" name="query" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
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
    <a href="{{ route('mesin.create') }}"><button class="btn btn-primary">Tambah Data</button></a>
    <div class="table-responsive">
        <table id="mesin_table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Jenis Mesin</th>
                    <th>Merk</th>
                    <th>Tahun Pembuatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mesin as $m)
                    <tr>
                        <td>{{ $m->nama_mesin }}</td>
                        <td>{{ $m->merek_mesin }}</td>
                        <td>{{ $m->created_at }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('mesin.edit', $m->id) }}">Edit</i></a>
                            <form action="{{ route('mesin.destroy', $m->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($mesin->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Data Mesin Kosong</td>
                        </tr>
                    @endif

                    </tbody>
        </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $mesin->links() }}
        </div>
@endsection