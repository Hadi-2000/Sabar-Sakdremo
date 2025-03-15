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
    <form action="{{ route('perbaikan.update', $perbaikan->id) }}" method="post">
        @csrf
        @method('put')
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Teknisi</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{$perbaikan->teknisi}}" required placeholder="Masukan Nama Teknisi">
        </div>
        <!-- Input Tanggal -->
        <div class="mb-3">
            <label for="tanggal">Mesin</label>
            <select name="mesin" id="mesin" class="form-control">
                @foreach($mesin as $m)
                    <option value="{{$m->nama_mesin }}" {{ $m->id == $perbaikan->id_mesin ? 'selected' : '' }}>{{ $m->nama_mesin }}</option>
                @endforeach
            </select>
        </div>
        <!-- Input Keperluan -->
        <div class="mb-3">
            <label for="keterangan">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" required rows="3" placeholder="Masukan Keterangan">{{$perbaikan->keterangan}}</textarea>
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="Pending" {{ $perbaikan->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Dikerjakan" {{ $perbaikan->status == 'Dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                <option value="Selesai" {{ $perbaikan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
            </select>
        <div class="mb-3">
            <label for="jumlah">Biaya</label>
            <input type="text" class="form-control" id="jumlah" name="jumlah" value="{{$perbaikan->biaya}}" oninput="formatUangInput(this)" required placeholder="Masukan Biaya">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Ubah</button>
    </form>
    <script src="{{ asset('js/formatUangInput.js')}}"></script>
@endsection