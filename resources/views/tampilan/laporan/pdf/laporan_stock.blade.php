<h1 style="text-align: center;">Laporan Stock</h1>
<h1 style="text-align: center">Tanggal {{$tgl}}</h1>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $s)
        <tr>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->stock }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
