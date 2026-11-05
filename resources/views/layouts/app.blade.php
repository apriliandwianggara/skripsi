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

        /**
         * Nada Dering Pesanan Selesai / Siap Diambil Sekarsa Coffee
         * Nada melodi ramah (Melodic Chime 4 Nada + Harmoni Bell)
         * Nada: C5 (523.25Hz) -> E5 (659.25Hz) -> G5 (783.99Hz) -> C6 (1046.50Hz)
         */
        function playSekarsaChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();

                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                const notes = [
                    { freq: 523.25, time: 0.00, dur: 0.6 },  // C5
                    { freq: 659.25, time: 0.18, dur: 0.6 },  // E5
                    { freq: 783.99, time: 0.36, dur: 0.7 },  // G5
                    { freq: 1046.50, time: 0.54, dur: 1.6 }, // C6 (High Ding panjang)
                ];

                notes.forEach(note => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    // Menggunakan triangle + sine untuk nada jernih seperti bel kafe/airport chime
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(note.freq, ctx.currentTime + note.time);

                    gain.gain.setValueAtTime(0.001, ctx.currentTime + note.time);
                    gain.gain.exponentialRampToValueAtTime(0.45, ctx.currentTime + note.time + 0.04);
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + note.time + note.dur);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + note.time);
                    osc.stop(ctx.currentTime + note.time + note.dur);

                    // Tambahan nada pendukung (harmoni hangat)
                    const subOsc = ctx.createOscillator();
                    const subGain = ctx.createGain();
                    subOsc.type = 'sine';
                    subOsc.frequency.setValueAtTime(note.freq * 2, ctx.currentTime + note.time);
                    subGain.gain.setValueAtTime(0.12, ctx.currentTime + note.time);
                    subGain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + note.time + note.dur * 0.8);
                    subOsc.connect(subGain);
                    subGain.connect(ctx.destination);
                    subOsc.start(ctx.currentTime + note.time);
                    subOsc.stop(ctx.currentTime + note.time + note.dur * 0.8);
                });

                // Getar HP jika browser smartphone mendukung Vibration API
                if ('vibrate' in navigator) {
                    navigator.vibrate([300, 150, 300, 150, 500]);
                }
            } catch (e) {
                console.warn('Audio Chime tidak dapat dimainkan:', e);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

