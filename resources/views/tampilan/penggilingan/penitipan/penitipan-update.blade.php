@extends('layouts.layout1')

@section('container')
    <div class="judul-container">Tambah Pelanggan</div>
    <div>
        <form action="{{ route('penitipan.update', $pelanggan->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" class="suggestion-item form-control" id="nama_pelanggan" name="nama_pelanggan" value="{{$pelanggan->nama}}" readonly>
            </div>
                <div class="mb-3">
                    <label for="barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="barang" name="barang" value="{{$penitipan->barang}}" placeholder="Masukan Nama Barang">
                </div>
    
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Barang (Kg)</label>
                    <input type="hidden" name="jumlah_hidden" id="jumlah_hidden">
                    <input type="text" class="form-control" id="jumlah" name="jumlah" value="{{$penitipan->jumlah}}" placeholder="Masukan jumlah barang(kg)" oninput="formatUangInput(this)">
                </div>
            <div class="mb-3">
                <label for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="Menitipkan" {{ $penitipan->status == 'Menitipkan' ? 'selected' : '' }}>Menitipkan</option>
                    <option value="Tidak Menitipkan" {{ $penitipan->status == 'Tidak Menitipkan' ? 'selected' : '' }}>Tidak Menitipkan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <script src="{{asset('js/dashboard.js')}}"></script>
    </div>
@endsection