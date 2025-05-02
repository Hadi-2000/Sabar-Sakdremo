@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Arus Kas</h1>
</div>
@if (session('success'))
    <h3 class="text-success text-center">{{ session('success')}}</h3>
@endif 
@if (session('error'))
    <h3 class="text-danger text-center">{{ session('error')}}</h3>
@endif 
<div class="row">
    <div id="accordion">
        <!-- Form Filter Tanggal -->
        <form class="d-flex kas-search mb-3" method="GET" action="{{route('laporan.kas.search')}}">
            @if (!isset($start) && !isset($end))
                <input class="form-control me-2" type="date" name="start_date">
                <p class="mx-2">-sampai-</p>
                <input class="form-control me-2" type="date" name="end_date">
            @else
                <input class="form-control me-2" type="date" name="start_date" value="{{$start}}">
                <p class="mx-2">-sampai-</p>
                <input class="form-control me-2" type="date" name="end_date" value="{{$end}}">
            @endif
           
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <table class="table table-bordered mt-2">
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Jenis</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Jumlah</th>
            </tr>
            @if ($arus->isEmpty())
                <tr>
                    <td colspan="4" class="text-center text-danger">Data tidak ditemukan</td>
                </tr>
            @endif
            @foreach ($arus as $kas)
            <tr>
                <td>{{$kas->updated_at->format('Y-m-d')}}</td>
                <td>{{$kas->jenis_kas}}</td>
                <td>{{$kas->keterangan}}</td>
                @if ($kas->jenis_transaksi == 'Masuk')
                    <td class="text-success">Rp. {{number_format($kas->jumlah,0,',','.')}}</td>
                @elseif ($kas->jenis_transaksi == 'Keluar')
                    <td class="text-danger"> - Rp. {{number_format($kas->jumlah,0,',','.')}}</td>
                @endif
            </tr>
            @endforeach
        </table>

        <!-- Tombol Download -->
        <form method="GET" action="{{route('laporan.kas.download')}}">
            <br><hr><br>
            <strong><h2 class="text-center">Form Download</h2></strong>
            <p>Tanggal Awal</p>
            <input class="form-control me-2" type="date" name="start_date"><br>
            <p>Tanggal Akhir</p>
            <input class="form-control me-2" type="date" name="end_date"><br>
            <button class="btn btn-outline-success mb-3" type="submit">Mendownload</button>
        </form>
    </div>
</div>
@endsection
