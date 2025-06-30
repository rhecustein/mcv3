<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 0; }
        .subtitle { font-size: 14px; color: #555; }
    </style>
</head>
<body>
    <h2>Rekap Surat Terbit</h2>
    <p class="subtitle">Periode: {{ request('start_date') }} s.d. {{ request('end_date') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Jenis</th>
                <th>Dokter</th>
                <th>Klinik</th>
                <th>Perusahaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['pasien'] }}</td>
                    <td>{{ $row['jenis'] }}</td>
                    <td>{{ $row['dokter'] }}</td>
                    <td>{{ $row['klinik'] }}</td>
                    <td>{{ $row['perusahaan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
