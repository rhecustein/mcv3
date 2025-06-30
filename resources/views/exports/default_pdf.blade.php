<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan' }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #555;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <h2>{{ strtoupper($title ?? 'Laporan') }}</h2>
    <p class="subtitle">
        Periode: {{ request('start_date') ?? '-' }} s.d. {{ request('end_date') ?? '-' }}
    </p>

    @if (count($data) > 0)
        <table>
            <thead>
                <tr>
                    @foreach (array_keys($data[0]) as $key)
                        <th>{{ strtoupper(str_replace('_', ' ', $key)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p><i>Tidak ada data ditemukan untuk periode ini.</i></p>
    @endif

</body>
</html>
