@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Stock</h1>
</div>
@if (session('success'))
    <h3 class="text-success text-center">{{ session('success')}}</h3>
@endif 
@if (session('error'))
    <h3 class="text-danger text-center">{{ session('error')}}</h3>
@endif 
<div class="row">
    <div id="accordion">
        <table class="table table-bordered mt-2">
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Stock</th>
            </tr>
            @if ($stock->isEmpty())
                <tr>
                    <td colspan="3" class="text-center text-danger">Data tidak ditemukan</td>
                </tr>
            @endif
            @foreach ($stock as $s)
            <tr>
                <td>{{$s->updated_at->format('Y-m-d')}}</td>
                <td>{{$s->nama}}</td>
                <td>{{$s->stock}}</td>
            </tr>
            @endforeach
        </table>

        <!-- Tombol Download -->
        <form method="GET" action="{{route('laporan.stock.download')}}">
            <br><br>
            <button class="btn btn-outline-success mb-3" type="submit">Mendownload</button>
        </form>
    </div>
</div>
@endsection
