@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Tambah Data Utang</h1>
</div>

<div class="ms-5 me-5 p-4 border rounded shadow w-60">
    <form action="{{ route('utang.store') }}" method="post">
        @csrf
        <!-- Input Nama Pelanggan -->
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
            <!-- Input jumlah per stok jika ambil dari Stock -->
            <div class="mb-3" id="jumlah_input_container">
                <label for="jumlah" class="form-label">Total (Rp)</label>
                <input type="hidden" name="jumlah_hidden" id="jumlah_hidden">
                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Masukan jumlah" oninput="formatUangInput(this)">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Masukan keterangan"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
    <button id="cek_button" class="btn btn-primary mt-2">Cek</button>
</div>

<script>
    var cekPelangganUrl = "{{ route('keuangan.utang.create.cek-pelanggan') }}";
    var cekPelangganAuto = "{{ route('keuangan.utang.create.cek-auto')}}"; // Buat endpoint untuk cek stock di backend

    $(document).ready(function() {
        // Event listener untuk mengubah tampilan ketika pilihan "Dari" berubah
        $('#ambil').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue === 'OnHand' || selectedValue === 'Operasional') {
                $('#jumlah_input_container').show();
                $('#jumlah').val(''); // Kosongkan input jumlah
            } else {
                $('#jumlah_input_container').hide();
            }
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('js/formatUangInput.js')}}"></script>
<script src="{{asset('js/utangPiutang_cek-pelanggan.js')}}"></script>
@endsection