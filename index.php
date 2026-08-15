<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Portal - SIMKES Khanza Mobile JKN Bridging</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0b0f17;
            --card-bg: rgba(22, 30, 46, 0.75);
            --card-hover: rgba(30, 41, 64, 0.9);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-blue: #38bdf8;
            --accent-cyan: #06b6d4;
            --accent-green: #22c55e;
            --accent-purple: #a855f7;
            --accent-amber: #f59e0b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #070a10 0%, #0f172a 100%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        /* Navbar Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 16px 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .brand-title { display: flex; align-items: center; gap: 16px; }

        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.4);
            font-weight: 700; font-size: 22px;
        }

        .brand-text h1 {
            font-size: 19px; font-weight: 700;
            background: linear-gradient(90deg, #ffffff, #93c5fd);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .brand-text p { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

        .nav-links {
            display: flex; align-items: center; gap: 8px;
        }

        .nav-item {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 10px;
            text-decoration: none; font-size: 13px; font-weight: 500;
            color: var(--text-muted);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-item:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active {
            color: #ffffff;
            background: rgba(56, 189, 248, 0.18);
            border-color: rgba(56, 189, 248, 0.4);
            font-weight: 600;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 32px 36px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-text h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-text p {
            font-size: 14px;
            color: var(--text-muted);
            max-width: 680px;
            line-height: 1.6;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.35);
            color: #4ade80;
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .pulse-dot {
            width: 9px; height: 9px;
            background-color: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 10px #22c55e;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* 3 Menu Cards Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 992px) {
            .menu-grid { grid-template-columns: 1fr; }
        }

        .menu-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            transition: all 0.3s ease;
        }

        .card-engine::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
        .card-kontrol::before { background: linear-gradient(90deg, #9333ea, #c084fc); }
        .card-tester::before { background: linear-gradient(90deg, #059669, #34d399); }

        .menu-card:hover {
            transform: translateY(-6px);
            background: var(--card-hover);
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
        }

        .card-badge {
            align-self: flex-start;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-blue { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
        .badge-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .badge-green { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }

        .card-header-box {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .card-icon {
            width: 54px; height: 54px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
        }

        .icon-blue { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .icon-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .icon-green { background: rgba(34, 197, 94, 0.15); color: #4ade80; }

        .card-title-text h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .card-title-text p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .card-desc {
            font-size: 13.5px;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-blue { background: rgba(56, 189, 248, 0.12); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
        .btn-purple { background: rgba(168, 85, 247, 0.12); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .btn-green { background: rgba(34, 197, 94, 0.12); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }

        .menu-card:hover .card-btn {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* System Info Footer Box */
        .info-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon"><i class="fa-solid fa-hospital"></i></div>
            <div class="brand-text">
                <h1>SIMKES Khanza - Mobile JKN Portal</h1>
                <p>Sistem Pengiriman & Monitoring Antrean BPJS Kesehatan</p>
            </div>
        </div>

        <nav class="nav-links">
            <a href="index.php" class="nav-item active"><i class="fa-solid fa-house"></i> Home</a>
            <a href="engine_sync.php" class="nav-item"><i class="fa-solid fa-bolt"></i> Engine Sync</a>
            <a href="monitoring_taskid.php" class="nav-item"><i class="fa-solid fa-chart-line"></i> Kontrol Task ID</a>
            <a href="test_single.php" class="nav-item"><i class="fa-solid fa-vial"></i> Tester Single</a>
        </nav>
    </header>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="hero-text">
            <h2>Portal Integrasi Antrean Mobile JKN</h2>
            <p>Pilih salah satu menu di bawah untuk mengelola siklus sinkronisasi antrean otomatis, melakukan audit kelengkapan Task ID 1 s.d. 7 per pasien, atau melakukan pengujian 1-by-1 secara manual.</p>
        </div>
        <div>
            <div class="status-pill">
                <div class="pulse-dot"></div>
                <span>Engine Ready & Connected</span>
            </div>
        </div>
    </div>

    <!-- 3 Main Menu Cards Grid -->
    <div class="menu-grid">
        <!-- Card 1: Engine Sync Worker -->
        <a href="engine_sync.php" class="menu-card card-engine">
            <span class="card-badge badge-blue">Auto Worker / Scheduler</span>
            <div class="card-header-box">
                <div class="card-icon icon-blue"><i class="fa-solid fa-bolt"></i></div>
                <div class="card-title-text">
                    <h3>Engine Sync Worker</h3>
                    <p>Auto Sync & Task Scheduler</p>
                </div>
            </div>
            <p class="card-desc">Jalankan siklus sinkronisasi otomatis (/antrean/add, updatewaktu 1-7, & batal). Dilengkapi live console log, timer 10 menit, & tombol stop.</p>
            <div class="card-btn btn-blue">
                <span>Buka Engine Sync Worker</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Card 2: Kontrol Task ID -->
        <a href="monitoring_taskid.php" class="menu-card card-kontrol">
            <span class="card-badge badge-purple">Audit & Monitoring</span>
            <div class="card-header-box">
                <div class="card-icon icon-purple"><i class="fa-solid fa-chart-line"></i></div>
                <div class="card-title-text">
                    <h3>Kontrol Task ID</h3>
                    <p>Matriks Audit 14 Kolom</p>
                </div>
            </div>
            <p class="card-desc">Inspeksi detail waktu Task 1 s.d. 7 seluruh pasien (BPJS & Onsite). Menampilkan status resep dan indikator kelengkapan pengiriman.</p>
            <div class="card-btn btn-purple">
                <span>Buka Kontrol Task ID</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Card 3: Tester Single Booking -->
        <a href="test_single.php" class="menu-card card-tester">
            <span class="card-badge badge-green">Testing & Debugging</span>
            <div class="card-header-box">
                <div class="card-icon icon-green"><i class="fa-solid fa-vial"></i></div>
                <div class="card-title-text">
                    <h3>Tester Single Booking</h3>
                    <p>Uji Coba Manual 1-by-1</p>
                </div>
            </div>
            <p class="card-desc">Pengujian pengiriman 1 kode booking / No. Rawat secara manual. Mendukung pilihan input tanggal & jam custom untuk keperluan testing.</p>
            <div class="card-btn btn-green">
                <span>Buka Single Tester</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Info Footer Box -->
    <div class="info-card">
        <div><i class="fa-solid fa-database"></i> SIMRS Database: <strong>Connected</strong></div>
        <div><i class="fa-solid fa-server"></i> Server Time: <strong><?= date('Y-m-d H:i:s T') ?></strong></div>
        <div><i class="fa-solid fa-code-branch"></i> Version: <strong>PHP Native Engine v2.0</strong></div>
    </div>
</div>

</body>
</html>
