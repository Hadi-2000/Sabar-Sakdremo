@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Laba Rugi</h1>
</div>
<div class="row">

    <!-- Konten Note -->
    <div class="col-md-9" id="accordion">
        <!-- Arus Kas -->
            <form class="d-flex kas-search mb-3" role="search">
                <input class="form-control me-2" type="date">
                <p>-sampai-</p>
                <input class="form-control me-2" type="date">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <button class="btn btn-outline-success" type="button">Mendownload</button>
            <!-- /.tampilanData -->
        </div>
</div>
@endsection