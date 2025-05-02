@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Piutang</h1>
</div>
<!-- Function -->
<script src="{{ asset('js/dashboard.js') }}"></script>


<div id="utang" data-bs-parent="#accordion" class="p-3 accordion-item">
    <form action="{{ route('keuangan.piutang.search') }}" method="GET" class="d-flex utang-search mb-3" role="search">
        <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search" value="{{request('query')}}">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    <a href="{{route('piutang.create')}}"> + Tambah Data</a>
    @if(session('success'))
        <p class="alert alert text-center">
            {{ session('success') }}
        </tr>
     @endif
    <table class="table table-bordered bg-light text-dark mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Jumlah</th>
                <th>Status Lunas</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if(session('error'))
                <tr class="alert alert-danger text-center">
                    <td colspan="7" class="text-center text-danger">{{ session('error') }}</td>
                </tr>
            @endif
            @if(isset($utang) && !$utang->isEmpty())
                @foreach ($utang as $item)
                    <tr>
                        <td>{{$item->updated_at}}</td>
                        <td>{{$item->nama}}</td>
                        <td>{{$item->alamat}}</td>
                        <td>Rp. {{number_format($item->nominal)}}</td>
                        <td>{{$item->status}}</td>
                        <td>{{$item->jenis}}</td>
                        <td>
                            <a class="btn btn-success" href="{{route('keuangan.piutang.lunas', $item->id)}}">Lunas</a>
                            <a class="btn btn-primary" href="{{route('piutang.edit', $item->id)}}">Edit</a>
                            <form method="post" action="{{route('piutang.destroy', $item->id)}}" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center text-danger bg-white">Data Pencarian Utang tidak ditemukan.</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $utang->links() }}
    </div>
</div>
@endsection