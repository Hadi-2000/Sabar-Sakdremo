@extends('layouts.layout1')

@section('container')
    
<div class="judul-container">
    <h1>Ubah Data</h1>
</div>
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif
<div>
    <form action="{{ route('stock.update', $stock->id) }}" method="post">
        @csrf
        @method('PUT')
        <!-- Input Nama -->
        <div class="mb-3">
            <label for="nama">Nama Produk</label>
            <select name="nama" id="nama" class="form-control" disabled>
                <option value="">Pilih Produk</option>
                @foreach($produk as $p)
                    <option value="{{$p->nama}}" 
                        @if($stock->nama == $p->nama) selected @endif>
                        {{ $p->nama }}
                    </option>
                @endforeach
            </select>
        </div>
         <!-- Input Stock -->
         <div class="mb-3">
            <label for="jumlah">Jumlah Stock yang dijual</label>
            <input type="hidden" name="jumlah_hidden" id="jumlah_hidden"> 
            <input type="text" class="form-control" value="{{$stock->stock}}" id="jumlah" name="jumlah" oninput="formatUangInput(this)" placeholder="Masukan Jumlah Stock">
        </div>
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div>
    <script type="text/javascript" src="{{asset('js/formatUangInput.js')}}"></script>
</div>
@endsection