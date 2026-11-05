<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sekarsa Coffee - Pemesanan Online')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sekarsa: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        ambercafe: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            800: '#92400e',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS for clean mobile experience -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .glass-header {
            background: rgba(20, 83, 45, 0.95);
            backdrop-filter: blur(8px);
        }
        .badge-fifo {
            background: linear-gradient(135deg, #15803d 0%, #064e3b 100%);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    @yield('content')

    <!-- Flash message alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#16a34a',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc2626'
                });
            @endif

            @if(isset($errors) && $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terdapat Kesalahan',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    confirmButtonColor: '#dc2626'
                });
            @endif
        });

        let sekarsaAlarmInterval = null;
        let tabTitleInterval = null;
        const originalDocTitle = document.title;

        // Minta izin notifikasi browser jika didukung
        function requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().catch(() => {});
            }
        }

        // Jalankan audio nada alarm kencang (Volume Maksimal)
        function playLoudAlarmTone() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                // Melodi alarm nyaring & tegas (C6 - E6 - G6 - C7 berulang)
                const chimeNotes = [
                    { freq: 1046.50, time: 0.00, dur: 0.22 }, // C6
                    { freq: 1318.51, time: 0.18, dur: 0.22 }, // E6
                    { freq: 1567.98, time: 0.36, dur: 0.25 }, // G6
                    { freq: 2093.00, time: 0.54, dur: 0.65 }, // C7 (High bell kencang)
                ];

                chimeNotes.forEach(n => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(n.freq, ctx.currentTime + n.time);

                    // Gain penuh (1.0) untuk suara kencang
                    gain.gain.setValueAtTime(0.01, ctx.currentTime + n.time);
                    gain.gain.exponentialRampToValueAtTime(1.0, ctx.currentTime + n.time + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + n.time + n.dur);

                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(ctx.currentTime + n.time);
                    osc.stop(ctx.currentTime + n.time + n.dur);

                    // Harmoni tambahan
                    const subOsc = ctx.createOscillator();
                    const subGain = ctx.createGain();
                    subOsc.type = 'sine';
                    subOsc.frequency.setValueAtTime(n.freq * 1.5, ctx.currentTime + n.time);
                    subGain.gain.setValueAtTime(0.3, ctx.currentTime + n.time);
                    subGain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + n.time + n.dur * 0.7);
                    subOsc.connect(subGain);
                    subGain.connect(ctx.destination);
                    subOsc.start(ctx.currentTime + n.time);
                    subOsc.stop(ctx.currentTime + n.time + n.dur * 0.7);
                });

                // Getar HP kuat berulang jika didukung
                if ('vibrate' in navigator) {
                    navigator.vibrate([500, 200, 500, 200, 800]);
                }
            } catch (e) {
                console.warn('Audio alarm error:', e);
            }
        }

        // Mulai dering alarm berulang tanpa henti
        function startSekarsaContinuousAlarm() {
            playLoudAlarmTone();

            if (!sekarsaAlarmInterval) {
                sekarsaAlarmInterval = setInterval(() => {
                    playLoudAlarmTone();
                }, 2200);
            }

            if (!tabTitleInterval) {
                tabTitleInterval = setInterval(() => {
                    document.title = (document.title.includes('🔔') ? '👉 PESANAN ANDA SIAP!' : '🔔🔔 SEKARSA COFFEE 🔔🔔');
                }, 700);
            }

            showBrowserNotification('🔔 PESANAN ANDA SIAP DIAMBIL!', 'Pesanan Anda sudah selesai. Silakan menuju kasir Sekarsa Coffee sekarang!');
        }

        // Hentikan alarm
        function stopSekarsaContinuousAlarm() {
            if (sekarsaAlarmInterval) {
                clearInterval(sekarsaAlarmInterval);
                sekarsaAlarmInterval = null;
            }
            if (tabTitleInterval) {
                clearInterval(tabTitleInterval);
                tabTitleInterval = null;
                document.title = originalDocTitle;
            }
            if ('vibrate' in navigator) {
                navigator.vibrate(0);
            }
        }

        function showBrowserNotification(title, body) {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const notif = new Notification(title, {
                        body: body,
                        icon: '/images/sekarsa-logo.jpg',
                        tag: 'sekarsa-order-ready',
                        renotify: true,
                        requireInteraction: true
                    });
                    notif.onclick = function() {
                        window.focus();
                        this.close();
                    };
                } catch (e) {
                    console.warn('Browser notif error:', e);
                }
            }
        }

        // Kompatibilitas mundur fungsi lama
        function playSekarsaChime() {
            startSekarsaContinuousAlarm();
        }
    </script>
    @stack('scripts')
</body>
</html>

