<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMKES Khanza - Service Mobile JKN Bridging Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0b0f17;
            --bg-card: rgba(22, 30, 46, 0.75);
            --bg-card-hover: rgba(30, 41, 64, 0.85);
            --border-card: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(59, 130, 246, 0.5);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --accent-cyan: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --font-main: 'Inter', sans-serif;
            --font-code: 'Fira Code', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(59, 130, 246, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.1) 0%, transparent 40%);
            background-attachment: fixed;
            padding: 24px;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 20px 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .brand-title {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
            font-weight: 700;
            font-size: 22px;
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 30px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
            font-size: 13px;
            font-weight: 600;
        }

        .status-pulse {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--success);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .kpi-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-card);
            border-radius: 14px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-glow);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .kpi-card.blue::before { background: var(--primary); }
        .kpi-card.green::before { background: var(--success); }
        .kpi-card.amber::before { background: var(--warning); }
        .kpi-card.cyan::before { background: var(--accent-cyan); }

        .kpi-title {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 8px;
            font-family: var(--font-code);
        }

        .kpi-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* Main Control Panel & Terminal Layout */
        .main-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
        }

        @media (max-width: 960px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Controls Sidebar */
        .panel-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 600;
            border-bottom: 1px solid var(--border-card);
            padding-bottom: 12px;
            color: var(--text-main);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .form-control {
            background: rgba(11, 15, 23, 0.6);
            border: 1px solid var(--border-card);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-main);
            font-family: var(--font-main);
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-card);
            color: var(--text-main);
            box-shadow: none;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
        }

        /* Toggle Switch */
        .toggle-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: rgba(11, 15, 23, 0.4);
            border: 1px solid var(--border-card);
            border-radius: 10px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #374151;
            transition: .3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--success);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Progress Timer */
        .timer-container {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .timer-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        .timer-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent-cyan));
            transition: width 1s linear;
        }

        /* Terminal Console */
        .terminal-card {
            background: #0d1117;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5);
            min-height: 480px;
        }

        .terminal-header {
            background: #161b22;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .terminal-dots {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot.red { background-color: #ff5f56; }
        .dot.yellow { background-color: #ffbd2e; }
        .dot.green { background-color: #27c93f; }

        .terminal-title {
            font-family: var(--font-code);
            font-size: 13px;
            color: var(--text-muted);
        }

        .terminal-body {
            padding: 16px;
            font-family: var(--font-code);
            font-size: 13px;
            line-height: 1.6;
            color: #d1d5db;
            overflow-y: auto;
            flex: 1;
            max-height: 520px;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .log-line {
            display: flex;
            gap: 12px;
            padding: 2px 0;
        }

        .log-line .line-num {
            color: #4b5563;
            user-select: none;
            min-width: 30px;
        }

        .log-line .line-content {
            flex: 1;
        }

        .log-tag-success { color: var(--success); }
        .log-tag-info { color: var(--primary); }
        .log-tag-warn { color: var(--warning); }
        .log-tag-error { color: var(--danger); }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon">🏥</div>
            <div class="brand-text">
                <h1>SIMKES Khanza - Service Mobile JKN Bridging</h1>
                <p>PHP Native Engine & Auto Synchronization Worker v2.0</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="test_single.php" style="background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.4); color: var(--accent-cyan); padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <span>🧪 Tester 1 Kode Booking</span>
            </a>
            <div class="status-badge">
                <div class="status-pulse"></div>
                <span>Engine Ready</span>
            </div>
        </div>
    </header>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-title">Add Antrean Sukses</div>
            <div class="kpi-value" id="kpiAdd">0</div>
            <div class="kpi-desc">Terkirim ke /antrean/add</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-title">Batal Antrean</div>
            <div class="kpi-value" id="kpiBatal">0</div>
            <div class="kpi-desc">Terkirim ke /antrean/batal</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-title">Task ID Terkirim</div>
            <div class="kpi-value" id="kpiTaskIdSuccess">0</div>
            <div class="kpi-desc">Task ID 1 - 7 & 99 Sukses</div>
        </div>
        <div class="kpi-card cyan">
            <div class="kpi-title">Terakhir Diperbarui</div>
            <div class="kpi-value" id="kpiLastSync" style="font-size: 20px;">--:--:--</div>
            <div class="kpi-desc">Waktu Eksekusi Sync</div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Controls Sidebar -->
        <div class="panel-card">
            <div class="panel-title">🎛️ Panel Kontrol Sync</div>

            <div class="form-group">
                <label for="tanggal1">Tanggal Mulai:</label>
                <input type="date" id="tanggal1" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="tanggal2">Tanggal Selesai:</label>
                <input type="date" id="tanggal2" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <button id="btnSync" class="btn">
                <span>🚀 Jalankan Sync Sekarang</span>
            </button>

            <div class="toggle-box">
                <span style="font-size: 13px; font-weight: 500;">Auto Sync (10 Min)</span>
                <label class="switch">
                    <input type="checkbox" id="autoSyncToggle" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="timer-container">
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted);">
                    <span>Hitung Mundur:</span>
                    <span id="timerText" style="font-weight: 600; color: var(--primary);">10:00</span>
                </div>
                <div class="timer-bar">
                    <div id="timerFill" class="timer-fill"></div>
                </div>
            </div>

            <button id="btnClearLog" class="btn btn-outline" style="margin-top: 10px;">
                <span>🗑️ Bersihkan Log</span>
            </button>
        </div>

        <!-- Terminal Console Card -->
        <div class="terminal-card">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <div class="dot red"></div>
                    <div class="dot yellow"></div>
                    <div class="dot green"></div>
                </div>
                <div class="terminal-title">Output Console Log (PHP Native Service Worker)</div>
                <div style="font-size: 12px; color: var(--text-muted);" id="logCount">0 baris</div>
            </div>
            <div class="terminal-body" id="terminalBody">
                <div class="log-line"><span class="line-num">1</span><span class="line-content log-tag-info">[SYSTEM] Service Mobile JKN Bridging Dashboard SIAP.</span></div>
                <div class="log-line"><span class="line-num">2</span><span class="line-content">[SYSTEM] Klik 'Jalankan Sync Sekarang' atau aktifkan Auto Sync 10 Menit.</span></div>
            </div>
        </div>
    </div>
</div>

<script>
    const btnSync = document.getElementById('btnSync');
    const btnClearLog = document.getElementById('btnClearLog');
    const terminalBody = document.getElementById('terminalBody');
    const autoSyncToggle = document.getElementById('autoSyncToggle');
    const timerText = document.getElementById('timerText');
    const timerFill = document.getElementById('timerFill');
    const logCount = document.getElementById('logCount');

    let lineCounter = 2;
    let timerSeconds = 600; // 10 menit
    let timerInterval = null;

    function addLog(text) {
        lineCounter++;
        const line = document.createElement('div');
        line.className = 'log-line';

        let tagClass = '';
        if (text.includes('Sukses') || text.includes('code: 200') || text.includes('code: 208') || text.includes('200 Ok')) {
            tagClass = 'log-tag-success';
        } else if (text.includes('Error') || text.includes('Gagal') || text.includes('Exception')) {
            tagClass = 'log-tag-error';
        } else if (text.includes('Menjalankan') || text.includes('JSON')) {
            tagClass = 'log-tag-info';
        }

        line.innerHTML = `<span class="line-num">${lineCounter}</span><span class="line-content ${tagClass}">${escapeHtml(text)}</span>`;
        terminalBody.appendChild(line);
        terminalBody.scrollTop = terminalBody.scrollHeight;
        logCount.innerText = lineCounter + ' baris';
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async function runSync() {
        btnSync.disabled = true;
        btnSync.innerHTML = '<span>⏳ Memproses Sync...</span>';

        const tgl1 = document.getElementById('tanggal1').value;
        const tgl2 = document.getElementById('tanggal2').value;

        addLog(`[ACTION] Memulai siklus sinkronisasi untuk periode ${tgl1} s.d. ${tgl2}...`);

        try {
            const response = await fetch(`service_antrol.php?tanggal1=${tgl1}&tanggal2=${tgl2}`);
            const data = await response.json();

            if (data.logs && Array.isArray(data.logs)) {
                data.logs.forEach(msg => addLog(msg));
            }

            if (data.stats) {
                document.getElementById('kpiAdd').innerText = data.stats.add_success || 0;
                document.getElementById('kpiBatal').innerText = data.stats.batal_success || 0;
                document.getElementById('kpiTaskIdSuccess').innerText = data.stats.taskid_success || 0;
            }

            const now = new Date();
            document.getElementById('kpiLastSync').innerText = now.toTimeString().split(' ')[0];

        } catch (err) {
            addLog(`[ERROR] Gagal menghubungi service_antrol.php: ${err.message}`);
        } finally {
            btnSync.disabled = false;
            btnSync.innerHTML = '<span>🚀 Jalankan Sync Sekarang</span>';
            resetTimer();
        }
    }

    function resetTimer() {
        timerSeconds = 600;
        updateTimerDisplay();
    }

    function updateTimerDisplay() {
        const mins = Math.floor(timerSeconds / 60);
        const secs = timerSeconds % 60;
        timerText.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        const pct = ((600 - timerSeconds) / 600) * 100;
        timerFill.style.width = `${pct}%`;
    }

    // Countdown Timer Loop
    setInterval(() => {
        if (autoSyncToggle.checked) {
            if (timerSeconds > 0) {
                timerSeconds--;
                updateTimerDisplay();
            } else {
                runSync();
            }
        }
    }, 1000);

    btnSync.addEventListener('click', runSync);
    btnClearLog.addEventListener('click', () => {
        terminalBody.innerHTML = '';
        lineCounter = 0;
        addLog('[SYSTEM] Log telah dibersihkan.');
    });

    // Jalankan sync pertama kali secara otomatis saat dibuka
    runSync();
</script>
</body>
</html>
