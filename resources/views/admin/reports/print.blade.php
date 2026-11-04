<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Sekarsa Coffee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box {
            margin-top: 15px;
            float: right;
            width: 250px;
            border: 1px solid #000;
            padding: 8px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 6px 16px; background: #15803d; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            Cetak Dokumen Laporan
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #e5e7eb; color: #111; border: none; border-radius: 4px; margin-left: 8px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>SEKARSA COFFEE & EATERY</h1>
        <p>Jl. Kaliurang KM 5.8 No. 24, Sleman, D.I. Yogyakarta | Telp: 0812-3456-7890</p>
        <p><strong>LAPORAN REKAPITULASI PENJUALAN & TRANSAKSI</strong></p>
        <p>Periode: {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>No. Pesanan</th>
                <th>Waktu Masuk</th>
                <th>Meja & Pelanggan</th>
                <th>Item Terjual</th>
                <th>Metode Bayar</th>
                <th>Status Bayar</th>
                <th class="text-right">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanans as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $p->nomor_pesanan }}</strong></td>
                <td>{{ $p->waktu_masuk->format('d/m/Y H:i') }}</td>
                <td>Meja {{ $p->meja->nomor_meja }} - {{ $p->nama_pelanggan }}</td>
                <td>
                    @foreach($p->items as $item)
                        {{ $item->jumlah }}x {{ $item->nama_menu }}<br>
                    @endforeach
                </td>
                <td>{{ strtoupper($p->metode_pembayaran ?? 'KASIR') }}</td>
                <td>{{ strtoupper($p->status_pembayaran) }}</td>
                <td class="text-right">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none;"><strong>Total Transaksi:</strong></td>
                <td style="border: none;" class="text-right"><strong>{{ $pesanans->count() }} Pesanan</strong></td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"><strong>Total Omzet Lunas:</strong></td>
                <td style="border: none;" class="text-right"><strong style="color: #15803d; font-size: 13px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 40px; display: flex; justify-content: space-between;">
        <div style="width: 200px; text-align: center;">
            <p>Mengetahui,</p>
            <p><strong>Owner Sekarsa Coffee</strong></p>
            <br><br><br>
            <p>( _______________________ )</p>
        </div>
        <div style="width: 200px; text-align: center;">
            <p>Yogyakarta, {{ now()->translatedFormat('d F Y') }}</p>
            <p><strong>Kasir Bertugas</strong></p>
            <br><br><br>
            <p>( {{ auth()->user()->name ?? 'Petugas Kasir' }} )</p>
        </div>
    </div>

</body>
</html>
