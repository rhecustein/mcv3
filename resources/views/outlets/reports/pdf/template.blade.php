<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 10px;
        }
        
        .outlet-info {
            font-size: 14px;
            color: #475569;
        }
        
        .report-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .report-info-row {
            display: table-row;
        }
        
        .report-info-cell {
            display: table-cell;
            padding: 4px 0;
            vertical-align: top;
        }
        
        .report-info-label {
            font-weight: bold;
            width: 150px;
            color: #374151;
        }
        
        .report-info-value {
            color: #6b7280;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        
        .data-table th {
            background-color: #3b82f6;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2563eb;
        }
        
        .data-table td {
            padding: 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .data-table tr:nth-child(odd) {
            background-color: white;
        }
        
        .summary-stats {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecfdf5;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }
        
        .summary-stats h3 {
            color: #065f46;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stats-cell {
            display: table-cell;
            padding: 5px 10px;
            width: 33.33%;
        }
        
        .stat-label {
            font-weight: bold;
            color: #047857;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        @if($outlet)
            <div class="outlet-info">
                <strong>{{ $outlet->name }}</strong>
                @if($outlet->address)
                    <br>{{ $outlet->address }}
                @endif
                @if($outlet->phone)
                    <br>Telp: {{ $outlet->phone }}
                @endif
            </div>
        @endif
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <div class="report-info-row">
            <div class="report-info-cell report-info-label">Jenis Laporan:</div>
            <div class="report-info-cell report-info-value">{{ $title }}</div>
        </div>
        <div class="report-info-row">
            <div class="report-info-cell report-info-label">Periode:</div>
            <div class="report-info-cell report-info-value">
                {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} - 
                {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
            </div>
        </div>
        <div class="report-info-row">
            <div class="report-info-cell report-info-label">Total Data:</div>
            <div class="report-info-cell report-info-value">{{ number_format(count($data)) }} records</div>
        </div>
        <div class="report-info-row">
            <div class="report-info-cell report-info-label">Dibuat oleh:</div>
            <div class="report-info-cell report-info-value">{{ $generated_by }}</div>
        </div>
        <div class="report-info-row">
            <div class="report-info-cell report-info-label">Dibuat pada:</div>
            <div class="report-info-cell report-info-value">{{ $generated_at }}</div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @if(in_array($type, ['rekap_surat', 'aktivitas_dokter', 'aktivitas_perusahaan']))
        <div class="summary-stats">
            <h3>Ringkasan Statistik</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-cell">
                        <div class="stat-label">Total Records:</div>
                        <div class="stat-value">{{ number_format(count($data)) }}</div>
                    </div>
                    @if($type === 'rekap_surat')
                        @php
                            $mcCount = collect($data)->filter(fn($row) => strtolower($row[3] ?? '') === 'mc')->count();
                            $skbCount = collect($data)->filter(fn($row) => strtolower($row[3] ?? '') === 'skb')->count();
                        @endphp
                        <div class="stats-cell">
                            <div class="stat-label">Surat Sakit (MC):</div>
                            <div class="stat-value">{{ number_format($mcCount) }}</div>
                        </div>
                        <div class="stats-cell">
                            <div class="stat-label">Surat Sehat (SKB):</div>
                            <div class="stat-value">{{ number_format($skbCount) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Data Table -->
    @if(count($data) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell ?: '-' }}</td>
                        @endforeach
                    </tr>
                    @if(($index + 1) % 25 === 0 && $index + 1 < count($data))
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach($headers as $header)
                                        <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ditemukan data untuk periode dan filter yang dipilih.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ $generated_at }}</p>
        <p>{{ $outlet->name ?? 'Health Management System' }} - Confidential Document</p>
    </div>
</body>
</html>