@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Tambah Data</h1>
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
    <form action="{{ route('perbaikan.store') }}" method="post">
        @csrf
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Teknisi</label>
            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukan Nama Teknisi">
        </div>
        <!-- Input Tanggal -->
        <div class="mb-3">
            <label for="mesin">Mesin</label>
            <select name="mesin" id="mesin" class="form-control">
                @foreach($mesin as $m)
                    <option value="{{$m->nama_mesin }}">{{ $m->nama_mesin }}</option>
                @endforeach
            </select>
        </div>
        <!-- Input Keperluan -->
        <div class="mb-3">
            <label for="keterangan">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" required rows="3" placeholder="Masukan Keterangan"></textarea>
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="Pending">Pending</option>
                <option value="Dikerjakan">Dikerjakan</option>
                <option value="Selesai">Selesai</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="biaya">Biaya</label>
            <input type="text" class="form-control" id="jumlah" name="jumlah" oninput="formatUangInput(this)" required placeholder="Masukan Biaya">
        </div>
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Tambah</button>
    </form>
    <script src="{{ asset('js/formatUangInput.js')}}"></script>
@endsection