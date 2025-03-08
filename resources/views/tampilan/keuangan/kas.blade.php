@extends('layouts.layout1')

@section('container')
<div class="judul-container">
    <h1>Manajemen Laporan Kas</h1>
</div>
    <div id="kas" data-bs-parent="#accordion" class="accordion-collapse collapse show note-content bg-light p-3 accordion-item">
        <h2><b>Laporan Kas Masuk dan Keluar</b></h2><br<hr>
        <form action="{{ route('keuangan.kas.search')}}" method="GET" class="d-flex kas-search mb-3" role="search">
            <input class="form-control me-2" name="query" id="query" type="search" placeholder="Search" aria-label="Search" value="{{ request('query')}}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary">
        <a href="/dashboard/keuangan/kas/create"> + Tambah Data</a>
      </button>
      @if(session('success'))
        <p class="alert alert text-center">
            {{ session('success') }}
        </tr>
     @endif
      <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tgl Dibuat</th>
                <th>Tgl Diupdate</th>
                <th>Keterangan</th>
                <th>Jenis Kas</th>
                <th>Jenis Transaksi</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (session('error'))
                <tr class="alert alert-danger text-center">
                    <td colspan="6" class="text-center text-danger">{{ session('error') }}</td>
                </tr>
            @endif
    
            @if (!$arus->isEmpty())
                @foreach ($arus as $kas)
                    <tr>
                        <td>{{ $kas->created_at }}</td>
                        <td>{{ $kas->updated_at }}</td>
                        <td>{{ $kas->keterangan }}</td>
                        <td>{{ $kas->jenis_kas }}</td>
                        <td>{{ $kas->jenis_transaksi }}</td>
                        <td>{{ "Rp. ".number_format($kas->jumlah, 0, ',', '.') }}</td>
                        <td>
                          <a href="{{ route('keuangan.kas.update', $kas->id) }}" class="btn btn-primary">
                            Edit
                          </a>
                            <form action="{{ route('keuangan.kas.destroy', $kas->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Hapus</button>
                          </form>
                        </td>
                    </tr>
      
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">Data Kosong</td>
                </tr>
            @endif

        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $arus->links() }}
    </div>
    </div>

    <script src="{{ asset('js/formatUangInput.js') }}"></script>
@endsection