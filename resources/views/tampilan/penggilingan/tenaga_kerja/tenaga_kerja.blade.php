@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Manajemen Penggilingan Tenaga Kerja</h1>
</div>
<div class="row">
            <form action="{{route('tenaga_kerja.search')}}" class="d-flex utang-search mb-3" role="search">
                <input class="form-control me-2" id="query" name="query" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <a href="{{route('tenaga_kerja.create')}}"> + Tambah Data</a>
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
            <table class="table table-bordered bg-light text-dark mt-3">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No Telepon</th>
                        <th>Status</th>
                        <th>Kehadiran</th>
                        <th>Gaji</th>
                        <th>Beban Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($pegawai))
                        @foreach ($pegawai as $p)
                        <tr>
                            <td>{{$p->nama}}</td>
                            <td>{{$p->alamat}}</td>
                            <td>{{$p->no_telp}}</td>
                            <td>{{$p->status}}</td>
                            <td>
                                @if ($p->kehadiran == 'Tidak Hadir')
                                    <a class="btn btn-primary" href="{{route('penggilingan.tenaga_kerja.hadir',$p->id)}}" onclick="return confirm('Apa pegawai ini benar hadir?')">Hadir</a>
                                @elseif($p->kehadiran == 'Hadir')
                                    <a class="btn btn-danger" href="{{route('penggilingan.tenaga_kerja.tidak_hadir', $p->id)}}" onclick="return confirm('Apa pegawai ini sudah pulang?')">Pulang</a>
                                @else
                                    <p>Karyawan Telah Pulang</p>
                                @endif
                            </td>
                            <td>{{"Rp. ".number_format($p->gaji)}}</td>
                            <td>{{"Rp. ".number_format($p->beban_gaji)}} ,  <a class="btn btn-danger" href="{{route('penggilingan.tenaga_kerja.bayar_gaji', $p->id)}}" onclick="return confirm('Apa anda yakin akan membayar gaji tenaga kerja ini?')">Bayar Gaji</a></td>
                            <td>
                                <a class="btn btn-primary" href="{{route('tenaga_kerja.edit', $p->id)}}">Edit</a> ||
                                <form method="POST" action="{{route('tenaga_kerja.destroy', $p->id)}}" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>    
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Data Tidak Ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $pegawai->links() }}
            </div>
            <script  src="{{asset('js/dashboard.js')}}"></script>
</div>
@endsection