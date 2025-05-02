@extends('layouts.layout1')

@section('container')
    <div class="judul-container">
        <h1>Manajemen Stock</h1>
    </div>
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="d-flex justify-content-center">
        <div class="row col mb-2">
                <form action="{{ route('penggilingan.stock.search') }}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="query" id="query" placeholder="Cari data...">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
            <div class="row-md-6 text-md-start text-center mt-2 mt-md-0">
                <a href="{{ route('stock.create') }}" class="btn btn-success">+ Tambah Data</a>
            </div>
    
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Stock</th>
                        <th>Harga Satuan</th>
                        <th>Harga Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stock as $s)
                        <tr>
                            <td>{{ $s->updated_at->format('Y-m-d') }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->stock }}</td>
                            <td>
                                @php
                                    $produkItem = $produk->firstWhere('nama', $s->nama);
                                @endphp
                                @if ($produkItem)
                                    Rp. {{ number_format($produkItem->harga_satuan) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>Rp. {{ number_format($s->total) }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('stock.edit', $s->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                                <form action="{{ route('stock.destroy', $s->id) }}" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')" method="post" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
    
                    @if($stock->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">Data Tidak Ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    
        <div class="d-flex justify-content-center">
            {{ $stock->links() }}
        </div>
    </div>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection