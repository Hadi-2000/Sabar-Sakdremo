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
<div>
    <form action="{{ route('stock.store') }}" method="post">
        @csrf
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Produk</label>
            <select name="nama" id="nama">
                <option value="">Pilih Produk</option>
                @foreach($produk as $p)
                    <option value="{{$p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
        <!-- Input Stock -->
        <div class="mb-3">
            <label for="jumlah">Jumlah Stock</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden"> 
            <input type="text" class="form-control" id="jumlah" name="jumlah" oninput="formatUangInput(this)" placeholder="Masukan Jumlah Stock">
        </div>
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div>
    <script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
    <script type="text/javascript">
        const urlSearch = {{route('penggilingan.stock.create.cek')}};
        const urlAuto = {{route('penggilingan.stock.create.auto')}};
    </script>
</div>
@endsection