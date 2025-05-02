@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Manajemen Penggilingan Produk</h1>
    </div>
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-danger" role="alert">
                {{ session('success') }}

            </div>
        @endif
        <div id="accordion">
            <!-- penitipan -->
                <form action="{{route('penggilingan.aset.search')}}" method="get" class="d-flex kas-search mb-3" role="search">
                    <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                <a href="{{route('aset.create')}}"> + Tambah Data</a>
                <div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Harga Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($produk))
                            @foreach ($produk as $p)
                                <tr>
                                    <td class="text-nowrap">{{$p->updated_at->format('Y-m-d')}}</td>
                                    <td>{{$p->nama}}</td>
                                    <td>{{$p->deskripsi}}</td>
                                    <td>{{$p->harga_satuan}}</td>
                                    <td class="d-flex m-1">
                                        <a class="btn btn-primary me-1" href="{{route('aset.edit', $p->id)}}">Update</a>
                                        <form method="POST" action="{{route('aset.destroy', $p->id)}}" onsubmit="return confirm('Anda yakin ingin menghapus data ini?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>  
                            @endforeach
                        @else
                        <tr>
                            <td colspan="5">Data Tidak Ditemukan</td>
                        </tr> 
                        @endif
                    </tbody>
                </table>
            </div>
            </div>
            <script src="{{asset('js/dashboard.js')}}"></script>
    </div>

@endsection