@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Ubah Data Utang</h1>
</div>

<div class="ms-5 me-5 p-4 border rounded shadow w-60">
    <form action="{{ route('utang.update', $utang->id) }}" method="post">
        @csrf
        @method('put')
        <!-- Input Nama Pelanggan -->
        <div class="mb-3">
            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
            <input type="disable" class="suggestion-item form-control" readonly style="pointer-events: none;" value="{{$utang->nama}}" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukan Nama Pelanggan">
        </div>

        <!-- Form tambahan (disembunyikan awalnya) -->
            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis</label>
                <select class="form-select" id="jenis" name="jenis">
                    <option value="Utang" {{$utang->jenis == 'Utang' ? 'Selected' : ''}}>Utang</option>
                    <option value="Piutang" {{$utang->jenis == 'Piutang' ? 'Selected' : ''}}>Piutang</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="ambil" class="form-label">Dari</label>
                <select class="form-select" id="ambil" name="ambil">
                    <option value="OnHand" {{$utang->ambil == 'OnHand' ? 'Selected':''}}>OnHand</option>
                    <option value="Operasional" {{$utang->ambil == 'Operasional' ? 'Selected' :''}}>Operasional</option>
                    <option value="Stock" {{$utang->ambil == 'Stock' ? 'Selected' :''}}>Stock</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Masukan keterangan">{{$utang->keterangan}}</textarea>
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Total (Rp)</label>
                <input type="hidden" name="jumlah_hidden" id="jumlah_hidden">
                <input type="text" class="form-control" value="{{$utang->nominal}}" id="jumlah" name="jumlah" placeholder="Masukan jumlah utang" oninput="formatUangInput(this)">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="Belum Lunas" {{$utang->status == 'Belum Lunas' ? 'Selected' : ''}}>Belum Lunas</option>
                    <option value="Lunas" {{$utang->status == 'Lunas' ? 'Selected' : ''}}>Lunas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
<script>
      var cekPelangganUrl = "{{ route('keuangan.utang.create.cek-pelanggan') }}";
      var cekPelangganAuto = "{{ route('keuangan.utang.create.cek-auto')}}"
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('js/formatUangInput.js')}}"></script>
<script src="{{asset('js/utangPiutang_cek-pelanggan.js')}}"></script>
@endsection
