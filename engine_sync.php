<?php require_once __DIR__ . '/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engine Sync Worker - SIMKES Khanza Mobile JKN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-body: #0b0f17;
            --bg-card: rgba(22, 30, 46, 0.75);
            --bg-card-hover: rgba(30, 41, 64, 0.85);
            --border-card: rgba(255, 255, 255, 0.08);
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

        * { box-sizing: border-box; margin: 0; padding: 0; }

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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Header Navigation Navbar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 16px 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .brand-title { display: flex; align-items: center; gap: 16px; }

        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
            font-weight: 700; font-size: 20px;
        }

        .brand-text h1 {
            font-size: 19px; font-weight: 700;
            background: linear-gradient(to right, #ffffff, #93c5fd);
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
            background: rgba(59, 130, 246, 0.18);
            border-color: rgba(59, 130, 246, 0.4);
            font-weight: 600;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .kpi-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative; overflow: hidden;
        }

        .kpi-card::after {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
        }

        .kpi-card.blue::after { background: var(--primary); }
        .kpi-card.amber::after { background: var(--warning); }
        .kpi-card.green::after { background: var(--success); }
        .kpi-card.cyan::after { background: var(--accent-cyan); }

        .kpi-title { font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
        .kpi-value { font-size: 30px; font-weight: 700; margin: 6px 0; color: #fff; }
        .kpi-desc { font-size: 12px; color: var(--text-muted); }

        /* Layout Grid */
        .main-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
        }

        @media (max-width: 992px) {
            .main-layout { grid-template-columns: 1fr; }
        }

        .panel-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 24px;
            display: flex; flex-direction: column; gap: 18px;
        }

        .panel-title { font-size: 16px; font-weight: 600; border-bottom: 1px solid var(--border-card); padding-bottom: 12px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 13px; color: var(--text-muted); }

        .form-control {
            background: rgba(11, 15, 23, 0.6);
            border: 1px solid var(--border-card);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-main);
            font-size: 14px; outline: none;
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #ffffff; border: none; border-radius: 10px;
            padding: 12px; font-size: 14px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s ease;
        }

        .btn:hover { transform: translateY(-1px); opacity: 0.95; }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-card);
            color: var(--text-muted);
        }

        .toggle-box {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px; background: rgba(11, 15, 23, 0.4);
            border-radius: 10px; border: 1px solid var(--border-card);
        }

        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #374151; transition: .3s; border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
            background-color: white; transition: .3s; border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--success); }
        input:checked + .slider:before { transform: translateX(20px); }

        .timer-container { display: flex; flex-direction: column; gap: 6px; }
        .timer-bar { width: 100%; height: 6px; background: rgba(255, 255, 255, 0.08); border-radius: 10px; overflow: hidden; }
        .timer-fill { width: 0%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--accent-cyan)); transition: width 1s linear; }

        /* Terminal Console */
        .terminal-card {
            background: #0d1117;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            overflow: hidden;
            display: flex; flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .terminal-header {
            background: #161b22;
            padding: 14px 20px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .terminal-dots { display: flex; gap: 8px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot.red { background-color: #ff5f56; }
        .dot.yellow { background-color: #ffbd2e; }
        .dot.green { background-color: #27c93f; }

        .terminal-title { font-family: var(--font-code); font-size: 13px; color: var(--text-muted); }

        .terminal-body {
            padding: 18px; font-family: var(--font-code); font-size: 13px;
            line-height: 1.6; color: #d1d5db;
            overflow-y: auto; flex: 1; max-height: 520px;
            white-space: pre-wrap; word-break: break-all;
        }

        .log-line { display: flex; gap: 12px; border-bottom: 1px dashed rgba(255, 255, 255, 0.03); padding: 2px 0; }
        .log-line .line-num { color: #4b5563; user-select: none; min-width: 30px; }
        .log-line .line-content { flex: 1; }

        .log-tag-success { color: var(--success); }
        .log-tag-info { color: var(--primary); }
        .log-tag-warn { color: var(--warning); }
        .log-tag-error { color: var(--danger); }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon"><i class="fa-solid fa-bolt"></i></div>
            <div class="brand-text">
                <h1>Engine Sync Worker Auto Service</h1>
                <p>SIMKES Khanza - Engine Bridging & Task Scheduler BPJS</p>
            </div>
        </div>

        <nav class="nav-links">
            <a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i> Home</a>
            <a href="engine_sync.php" class="nav-item active"><i class="fa-solid fa-bolt"></i> Engine Sync</a>
            <a href="monitoring_taskid.php" class="nav-item"><i class="fa-solid fa-chart-line"></i> Kontrol Task ID</a>
            <a href="test_single.php" class="nav-item"><i class="fa-solid fa-paper-plane"></i> Kirim Single</a>
            <a href="test_batch.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Kirim Bulk</a>
            <a href="dashboard_waktutunggu.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard BPJS</a>
        </nav>
    </header>

    <!-- KPI Cards Grid -->
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
            <div class="kpi-desc">Task ID 1 - 7 Sukses</div>
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
            <div class="panel-title"><i class="fa-solid fa-sliders"></i> Panel Kontrol Sync</div>

            <div class="form-group">
                <label for="tanggal1">Tanggal Mulai:</label>
                <input type="date" id="tanggal1" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="tanggal2">Tanggal Selesai:</label>
                <input type="date" id="tanggal2" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <button id="btnSync" class="btn">
                <span><i class="fa-solid fa-paper-plane"></i> Jalankan Sync Sekarang</span>
            </button>

            <button id="btnStopSync" class="btn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); margin-top: 4px; display: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                <span><i class="fa-solid fa-circle-stop"></i> Stop / Batal Sync</span>
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

            <button id="btnClearLog" class="btn btn-outline" style="margin-top: 6px;">
                <span><i class="fa-solid fa-trash-can"></i> Bersihkan Log</span>
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
                <div class="log-line"><span class="line-num">1</span><span class="line-content log-tag-info">[SYSTEM] Service Mobile JKN Bridging Engine Worker SIAP.</span></div>
            </div>
        </div>
    </div>
</div>

<script>
    const btnSync = document.getElementById('btnSync');
    const btnStopSync = document.getElementById('btnStopSync');
    const btnClearLog = document.getElementById('btnClearLog');
    const terminalBody = document.getElementById('terminalBody');
    const autoSyncToggle = document.getElementById('autoSyncToggle');
    const timerText = document.getElementById('timerText');
    const timerFill = document.getElementById('timerFill');
    const logCount = document.getElementById('logCount');

    let lineCounter = 1;
    let timerSeconds = 600; // 10 menit
    let currentAbortController = null;

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
        } else if (text.includes('dihentikan') || text.includes('dimatikan')) {
            tagClass = 'log-tag-warn';
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
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();
        const signal = currentAbortController.signal;

        btnSync.disabled = true;
        btnSync.innerHTML = '<span><i class="fa-solid fa-spinner fa-spin"></i> Memproses Sync...</span>';
        btnStopSync.style.display = 'block';

        const tgl1 = document.getElementById('tanggal1').value;
        const tgl2 = document.getElementById('tanggal2').value;

        addLog(`[ACTION] Memulai siklus sinkronisasi untuk periode ${tgl1} s.d. ${tgl2}...`);

        try {
            const response = await fetch(`service_antrol.php?tanggal1=${tgl1}&tanggal2=${tgl2}`, { signal });
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
            if (err.name === 'AbortError') {
                addLog(`[SYSTEM] Process sync telah dihentikan oleh pengguna.`);
            } else {
                addLog(`[ERROR] Gagal menghubungi service_antrol.php: ${err.message}`);
            }
        } finally {
            currentAbortController = null;
            btnSync.disabled = false;
            btnSync.innerHTML = '<span><i class="fa-solid fa-paper-plane"></i> Jalankan Sync Sekarang</span>';
            btnStopSync.style.display = 'none';
            resetTimer();
        }
    }

    function stopSync() {
        autoSyncToggle.checked = false;
        if (currentAbortController) {
            addLog(`[SYSTEM] Mengirim perintah Stop ke proses sync...`);
            currentAbortController.abort();
        } else {
            addLog(`[SYSTEM] Auto Sync 10 Menit telah dimatikan.`);
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
    btnStopSync.addEventListener('click', stopSync);
    btnClearLog.addEventListener('click', () => {
        terminalBody.innerHTML = '';
        lineCounter = 0;
        addLog('[SYSTEM] Log telah dibersihkan.');
    });

    // Otomatis jalankan siklus sync pertama saat halaman Engine Sync dibuka (Ideal untuk Task Scheduler)
    runSync();
</script>
</body>
</html>
