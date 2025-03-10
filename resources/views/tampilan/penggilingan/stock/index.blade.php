@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Manajemen Stock</h1>
    </div>
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <form action="{{route('penggilingan.stock.search')}}" method="get">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="query" id="query" placeholder="Cari data..." aria-label="Cari data..." aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <div class="col-md-6">
                <a href="{{route('penggilingan.stock.create')}}" class="btn btn-success">+ Tambah Data</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Stock</th>
                            <th>Harga Satuan</th>
                            <th>Harga Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stock as $s)
                            <tr>
                                <td>{{ $s->created_at }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>{{ $s->stock }}</td>
                                <td>{{ number_format($s->harga_satuan) }}</td>
                                <td>{{ number_format($s->total) }}</td>
                                <td class="d-flex">
                                    <a href="{{route('penggilingan.stock.update', $s->id)}}" class="btn btn-warning me-2">Edit</a>
                                    <form action="{{route('penggilingan.stock.destroy', $s->id)}}" method="post" style="display: inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($stock->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">Data Tidak Ditemukan</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $stock->links() }}
                    </div>
        </div>
    </div>
    <script src="{{asset('js/dashboard.js')}}"></script>

@endsection