<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan {{ $pesanan->nomor_pesanan }} - {{ $pengaturan->nama_cafe }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            width: 76mm;
            margin: 0 auto;
            padding: 8px 4px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .item-notes {
            font-size: 10px;
            font-style: italic;
            padding-left: 10px;
        }
        .no-print {
            margin-bottom: 15px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 6px 16px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Struk Transaksi
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; margin-left: 8px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="text-center">
        <h2 style="margin: 0; font-size: 16px;">{{ strtoupper($pengaturan->nama_cafe ?? 'SEKARSA COFFEE') }}</h2>
        <p style="margin: 2px 0; font-size: 11px;">{{ $pengaturan->alamat ?? 'Yogyakarta' }}</p>
        <p style="margin: 2px 0; font-size: 10px;">Telp: {{ $pengaturan->telepon ?? '-' }} &bull; IG: {{ $pengaturan->instagram ?? '-' }}</p>
    </div>

    <div class="divider"></div>

    <div style="font-size: 11px;">
        <div class="item-row">
            <span>No. Pesanan</span>
            <span class="bold">{{ $pesanan->nomor_pesanan }}</span>
        </div>
        <div class="item-row">
            <span>Nomor Meja</span>
            <span class="bold">Meja {{ $pesanan->meja->nomor_meja }}</span>
        </div>
        <div class="item-row">
            <span>Pelanggan</span>
            <span>{{ $pesanan->nama_pelanggan }}</span>
        </div>
        <div class="item-row">
            <span>Waktu Masuk</span>
            <span>{{ $pesanan->waktu_masuk->format('d/m/Y H:i') }}</span>
        </div>
        <div class="item-row">
            <span>Kasir</span>
            <span>{{ $pesanan->kasir ? $pesanan->kasir->name : 'Sistem Self-Order' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Items -->
    <div>
        @foreach($pesanan->items as $item)
        <div class="item-row">
            <span style="flex: 1;">{{ $item->jumlah }}x {{ $item->nama_menu }}</span>
            <span class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($item->catatan)
            <div class="item-notes">* {{ $item->catatan }}</div>
        @endif
        @endforeach
    </div>

    <div class="divider"></div>

    <!-- Calculations -->
    <div style="font-size: 11px;">
        <div class="item-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($pesanan->diskon_promo > 0)
        <div class="item-row">
            <span>Diskon ({{ $pesanan->promo ? $pesanan->promo->kode_promo : 'Promo' }})</span>
            <span>- Rp {{ number_format($pesanan->diskon_promo, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="item-row bold" style="font-size: 13px; margin-top: 4px;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($pesanan->total_akhir, 0, ',', '.') }}</span>
        </div>
        
        <div class="divider"></div>

        <div class="item-row">
            <span>Metode Bayar</span>
            <span class="bold">{{ strtoupper($pesanan->metode_pembayaran ?? 'TUNAI') }}</span>
        </div>
        <div class="item-row">
            <span>Status</span>
            <span class="bold">{{ strtoupper($pesanan->status_pembayaran) }}</span>
        </div>
        @if($pesanan->jumlah_bayar)
        <div class="item-row">
            <span>Bayar (Diterima)</span>
            <span>Rp {{ number_format($pesanan->jumlah_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="item-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($pesanan->kembalian, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 10px; margin-top: 8px;">
        <p style="margin: 0;">{{ $pengaturan->pesan_struk ?? 'Terima kasih atas pesanan Anda di Sekarsa Coffee!' }}</p>
        <p style="margin: 4px 0 0 0; color: #555;">Sistem FIFO Pemesanan QR Code</p>
    </div>

    <script>
        window.addEventListener('load', () => {
            // Uncomment auto-print if desired:
            // window.print();
        });
    </script>
</body>
</html>
