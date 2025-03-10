@extends('layouts.layout1')

@section('container')
    <div class="judul-container">Tambah Penitipan</div>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div>
        <form action="{{ route('penitipan.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" class="suggestion-item form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukan Nama Pelanggan">
                <span id="status_pelanggan" class="form-control text-danger"></span>
            </div>
    
            <!-- Form tambahan (disembunyikan awalnya) -->
            <div id="input_create" style="display: none;">
                <div class="mb-3">
                    <label for="alamat_pelanggan" class="form-label">Alamat Pelanggan</label>
                    <input type="text" class="form-control" id="alamat_pelanggan" name="alamat_pelanggan" placeholder="Masukan Alamat Pelanggan">
                </div>
    
                <div class="mb-3">
                    <label for="barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="barang" name="barang" placeholder="Masukan Nama Barang">
                </div>
    
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Barang (Kg)</label>
                    <input type="hidden" id="jumlah_hidden" name="jumlah_hidden">
                    <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Masukkan nominal" oninput="formatUangInput(this)">
                </div>
    
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
        <button id="cek_button" class="btn btn-primary mt-2">Cek</button>
    </div>
    <script>
          var cekPelangganUrl = "{{ route('penggilingan.penitipan.create.cek-pelanggan') }}";
          var cekPelangganAuto = "{{ route('penggilingan.penitipan.create.cek-auto')}}"
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('js/formatUangInput.js')}}"></script>
    <script src="{{asset('js/utangPiutang_cek-pelanggan.js')}}"></script>
@endsection