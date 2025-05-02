@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Penggilingan Penitipan</h1>
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
            <form action="{{route('penggilingan.penitipan.search')}}" method="get" class="d-flex kas-search mb-3" role="search">
                <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <a href="{{route('penitipan.create')}}"> + Tambah Data</a>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Barang</th>
                        <th>Jumlah</th> 
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($data))
                        @foreach ($data as $p)
                            <tr>
                                <td>{{$p->updated_at->format('Y-m-d')}}</td>
                                <td>{{$p->nama_pelanggan}}</td>
                                <td>{{$p->status}}</td>
                                <td>{{$p->barang}}</td>
                                <td>{{number_format($p->jumlah)}}</td>
                                <td><a class="btn btn-primary" href="{{route('penitipan.edit', $p->id)}}">Update</a> ||
                                    <form method="POST" action="{{route('penitipan.destroy', $p->id)}}" onsubmit="return confirm('Apakah anda ykin ingin menghapus data ini?')" style="display: inline;">
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
        <script src="{{asset('js/dashboard.js')}}"></script>
</div>
@endsection