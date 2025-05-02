<h1 style="text-align: center;">Laporan Arus Kas</h1>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($arus as $kas)
        <tr>
            <td>{{ $kas->updated_at->format('Y-m-d') }}</td>
            <td>{{ $kas->jenis_kas }}</td>
            <td>{{ $kas->keterangan }}</td>
            <td>
                @if ($kas->jenis_transaksi == 'Keluar')
                    -Rp. {{ number_format($kas->jumlah, 0, ',', '.') }}
                @else
                    Rp. {{ number_format($kas->jumlah, 0, ',', '.') }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
