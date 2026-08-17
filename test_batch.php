<?php require_once __DIR__ . '/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Bulk Booking - SIMKES Khanza Antrol</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0b0f17;
            --card-bg: rgba(22, 30, 46, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-blue: #38bdf8;
            --accent-cyan: #06b6d4;
            --accent-green: #22c55e;
            --accent-purple: #a855f7;
            --accent-amber: #f59e0b;
            --accent-red: #ef4444;
            --font-main: 'Outfit', sans-serif;
            --font-code: 'Fira Code', monospace;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-main);
            background: linear-gradient(135deg, #070a10 0%, #0f172a 100%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
            background-attachment: fixed;
        }

        .container {
            max-width: 1350px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
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
            font-weight: 700; font-size: 20px;
        }

        .brand-text h1 {
            font-size: 19px; font-weight: 700;
            background: linear-gradient(90deg, #ffffff, #93c5fd);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .brand-text p { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

        .nav-links { display: flex; align-items: center; gap: 8px; }

        .nav-item {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 10px;
            text-decoration: none; font-size: 13px; font-weight: 500;
            color: var(--text-muted); transition: all 0.2s ease; border: 1px solid transparent;
        }

        .nav-item:hover { color: var(--text-main); background: rgba(255, 255, 255, 0.05); }

        .nav-item.active {
            color: #ffffff; background: rgba(56, 189, 248, 0.18);
            border-color: rgba(56, 189, 248, 0.4); font-weight: 600;
        }

        /* Form Card */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            display: flex; flex-direction: column; gap: 20px;
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--card-border); padding-bottom: 14px;
        }

        .card-title {
            font-size: 16px; font-weight: 600; color: #ffffff;
            display: flex; align-items: center; gap: 10px;
        }

        .grid-form {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 900px) { .grid-form { grid-template-columns: 1fr; } }

        .form-group { display: flex; flex-direction: column; gap: 8px; }

        .form-group label {
            font-size: 12.5px; font-weight: 600; color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }

        .form-control {
            background: rgba(11, 15, 23, 0.7);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 12px 14px;
            color: var(--text-main);
            font-family: var(--font-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        textarea.form-control {
            font-family: var(--font-code);
            font-size: 13px;
            resize: vertical;
            min-height: 180px;
            line-height: 1.5;
        }

        .form-control:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
        }

        /* Controls Button Bar */
        .controls-bar {
            display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: flex-end;
            margin-top: 10px;
        }

        .btn {
            color: #ffffff; border: none; border-radius: 10px;
            padding: 12px 22px; font-size: 14px; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), #0284c7);
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.35);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.5);
        }

        .btn-amber {
            background: linear-gradient(135deg, var(--accent-amber), #d97706);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        }

        .btn-red {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        }

        .btn:disabled {
            opacity: 0.5; cursor: not-allowed; transform: none !important; box-shadow: none !important;
        }

        /* Progress Card */
        .progress-container {
            background: rgba(11, 15, 23, 0.6);
            border-radius: 12px;
            height: 14px;
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--card-border);
            margin: 10px 0;
        }

        .progress-bar-inner {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-blue));
            transition: width 0.3s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

        .stat-box {
            background: rgba(11, 15, 23, 0.5);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
        }

        .stat-box h5 { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-box p { font-size: 20px; font-weight: 700; color: #ffffff; margin-top: 4px; }

        /* Table Log */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--card-border);
            max-height: 450px;
        }

        table {
            width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;
        }

        thead { position: sticky; top: 0; background: rgba(15, 23, 42, 0.95); z-index: 5; }

        th {
            padding: 14px 16px; font-weight: 600; color: var(--accent-blue);
            border-bottom: 2px solid var(--card-border); white-space: nowrap;
        }

        td {
            padding: 12px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); color: var(--text-main); white-space: nowrap;
        }

        tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

        .badge-status {
            padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; font-family: var(--font-code);
            display: inline-block;
        }

        .badge-success { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge-amber { background: rgba(245, 158, 11, 0.2); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-cyan { background: rgba(6, 182, 212, 0.2); color: var(--accent-cyan); border: 1px solid rgba(6, 182, 212, 0.3); }

        /* Modal Inspector */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 100;
            padding: 20px;
        }

        .modal-content {
            background: #0f172a;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            width: 100%; max-width: 800px;
            max-height: 85vh;
            display: flex; flex-direction: column;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            overflow: hidden;
        }

        .modal-header {
            padding: 16px 20px; border-bottom: 1px solid var(--card-border);
            display: flex; justify-content: space-between; align-items: center;
        }

        .modal-body {
            padding: 20px; overflow-y: auto; flex: 1;
        }

        .code-box {
            background: #0d1117;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 16px;
            font-family: var(--font-code);
            font-size: 12px;
            color: #e5e7eb;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon"><i class="fa-solid fa-layer-group"></i></div>
            <div class="brand-text">
                <h1>Kirim Bulk Booking Mobile JKN</h1>
                <p>Pengiriman Massal Kode Booking / Task ID ke BPJS Kesehatan</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i> Home</a>
            <a href="engine_sync.php" class="nav-item"><i class="fa-solid fa-bolt"></i> Engine Sync</a>
            <a href="monitoring_taskid.php" class="nav-item"><i class="fa-solid fa-chart-line"></i> Kontrol Task ID</a>
            <a href="test_single.php" class="nav-item"><i class="fa-solid fa-paper-plane"></i> Kirim Single</a>
            <a href="test_batch.php" class="nav-item active"><i class="fa-solid fa-layer-group"></i> Kirim Bulk</a>
            <a href="dashboard_waktutunggu.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard BPJS</a>
        </nav>
    </header>

    <!-- Form Input Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-paste" style="color: var(--accent-cyan);"></i>
                <span>Input Daftar Kode Booking & Parameter Batch</span>
            </div>
        </div>

        <div class="grid-form">
            <!-- Left: Textarea Input -->
            <div class="form-group">
                <label for="inputBookingList">
                    <i class="fa-solid fa-list-ol"></i> Tempelkan Daftar Kode Booking / No. Rawat (Satu per baris):
                </label>
                <textarea id="inputBookingList" class="form-control" placeholder="Contoh:&#10;2026/08/17/000001&#10;2026/08/17/000002&#10;2026/08/17/000003"></textarea>
                <small style="font-size: 11.5px; color: var(--text-muted);">
                    Bisa berupa Kode Booking atau No Rawat (dipisahkan baris baru / enter).
                </small>
            </div>

            <!-- Right: Settings -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div class="form-group">
                    <label for="actionType"><i class="fa-solid fa-gears"></i> Tipe Aksi Batch:</label>
                    <select id="actionType" class="form-control">
                        <option value="check">🔍 0. Bulk Cek Status (DB SIMRS & BPJS)</option>
                        <option value="add">1. Bulk Tambah Antrean (/antrean/add)</option>
                        <option value="task1">2. Task ID 1 - Mulai Tunggu Poli</option>
                        <option value="task2">3. Task ID 2 - Mulai Pelayanan Loket</option>
                        <option value="task3">4. Task ID 3 - Selesai Pelayanan Loket</option>
                        <option value="task4">5. Task ID 4 - Mulai Pelayanan Dokter</option>
                        <option value="task5">6. Task ID 5 - Selesai Pelayanan Dokter</option>
                        <option value="task6">7. Task ID 6 - Selesai Racik Obat</option>
                        <option value="task7">8. Task ID 7 - Penyerahan Obat</option>
                        <option value="task99">9. Task ID 99 - Batal Antrean</option>
                        <option value="autofull" selected>⚡ 10. Bulk Auto Full Cycle (Task 1 s.d 7 Sekaligus)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="customWaktu"><i class="fa-regular fa-clock"></i> Waktu Custom Manual (Opsional):</label>
                    <input type="datetime-local" id="customWaktu" class="form-control">
                    <small style="font-size: 11px; color: var(--text-muted);">Kosongkan untuk Regulasi Utama Validasi / Fallback Ide A.</small>
                </div>

                <div class="form-group">
                    <label for="delayMs"><i class="fa-solid fa-hourglass-half"></i> Delay Antar Item (Milidetik):</label>
                    <input type="number" id="delayMs" class="form-control" value="300" min="100" max="5000">
                </div>
            </div>
        </div>

        <div class="controls-bar">
            <button id="btnStart" class="btn btn-primary"><i class="fa-solid fa-play"></i> <span>Jalankan Batch</span></button>
            <button id="btnPause" class="btn btn-amber" disabled><i class="fa-solid fa-pause"></i> <span>Pause</span></button>
            <button id="btnStop" class="btn btn-red" disabled><i class="fa-solid fa-stop"></i> <span>Hentikan</span></button>
        </div>
    </div>

    <!-- Progress & Real-time Stats Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-chart-line" style="color: var(--accent-purple);"></i>
                <span>Progress Batch & Log Real-time</span>
            </div>
            <span id="batchStateBadge" class="badge-status badge-cyan">Standby</span>
        </div>

        <div class="progress-container">
            <div id="progressBar" class="progress-bar-inner"></div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <h5>Total Antrean</h5>
                <p id="statTotal">0</p>
            </div>
            <div class="stat-box">
                <h5>Sukses</h5>
                <p id="statSuccess" style="color: var(--accent-green);">0</p>
            </div>
            <div class="stat-box">
                <h5>Gagal / Respon BPJS</h5>
                <p id="statDanger" style="color: var(--accent-red);">0</p>
            </div>
            <div class="stat-box">
                <h5>Pending</h5>
                <p id="statPending" style="color: var(--accent-amber);">0</p>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Booking / No. Rawat</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Aksi</th>
                        <th>Status Balikan</th>
                        <th>Keterangan / Message</th>
                        <th>Detail Raw</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada eksekusi batch. Tempelkan kode booking di atas dan klik <b>"Jalankan Batch"</b>.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Raw Response Viewer -->
<div id="modalInspector" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="font-size: 15px; color: #ffffff;" id="modalTitle">🔍 Detail Response Raw JSON</h3>
            <button onclick="closeModal()" style="background: none; border: none; color: var(--text-muted); font-size: 18px; cursor: pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalBodyCode" class="code-box"></div>
        </div>
    </div>
</div>

<script>
    const inputBookingList = document.getElementById('inputBookingList');
    const actionType = document.getElementById('actionType');
    const customWaktu = document.getElementById('customWaktu');
    const delayMsInput = document.getElementById('delayMs');

    const btnStart = document.getElementById('btnStart');
    const btnPause = document.getElementById('btnPause');
    const btnStop = document.getElementById('btnStop');

    const progressBar = document.getElementById('progressBar');
    const batchStateBadge = document.getElementById('batchStateBadge');

    const statTotal = document.getElementById('statTotal');
    const statSuccess = document.getElementById('statSuccess');
    const statDanger = document.getElementById('statDanger');
    const statPending = document.getElementById('statPending');

    const tableBody = document.getElementById('tableBody');
    const modalInspector = document.getElementById('modalInspector');
    const modalTitle = document.getElementById('modalTitle');
    const modalBodyCode = document.getElementById('modalBodyCode');

    let queue = [];
    let isRunning = false;
    let isPaused = false;
    let stopRequested = false;
    let currentIndex = 0;
    let successCount = 0;
    let dangerCount = 0;
    let executionResults = [];

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function parseBookingList() {
        const text = inputBookingList.value;
        const lines = text.split(/\r?\n/).map(s => s.trim()).filter(s => s.length > 0);
        return Array.from(new Set(lines)); // Deduplikasi
    }

    function updateStatsUI() {
        const total = queue.length;
        const processed = currentIndex;
        const pending = Math.max(0, total - processed);

        statTotal.innerText = total;
        statSuccess.innerText = successCount;
        statDanger.innerText = dangerCount;
        statPending.innerText = pending;

        const pct = total > 0 ? Math.round((processed / total) * 100) : 0;
        progressBar.style.width = pct + '%';
    }

    btnStart.addEventListener('click', async () => {
        const items = parseBookingList();
        if (items.length === 0) {
            alert('Silakan tempelkan setidaknya 1 Kode Booking / No Rawat di textarea!');
            return;
        }

        queue = items;
        currentIndex = 0;
        successCount = 0;
        dangerCount = 0;
        executionResults = [];
        isRunning = true;
        isPaused = false;
        stopRequested = false;

        btnStart.disabled = true;
        btnPause.disabled = false;
        btnStop.disabled = false;
        inputBookingList.disabled = true;
        actionType.disabled = true;

        batchStateBadge.className = 'badge-status badge-cyan';
        batchStateBadge.innerText = 'Memproses...';

        // Render Initial Empty Table Rows
        let tableHtml = '';
        queue.forEach((kb, idx) => {
            tableHtml += `
                <tr id="row-${idx}">
                    <td>${idx + 1}</td>
                    <td><span class="badge-status badge-cyan">${kb}</span></td>
                    <td id="name-${idx}">Menunggu...</td>
                    <td id="poli-${idx}">-</td>
                    <td>${actionType.options[actionType.selectedIndex].text}</td>
                    <td id="status-${idx}"><span class="badge-status badge-amber">Pending</span></td>
                    <td id="msg-${idx}">Antrean diproses...</td>
                    <td id="action-${idx}">-</td>
                </tr>
            `;
        });
        tableBody.innerHTML = tableHtml;
        updateStatsUI();

        // Loop Runner
        while (currentIndex < queue.length && !stopRequested) {
            if (isPaused) {
                batchStateBadge.className = 'badge-status badge-amber';
                batchStateBadge.innerText = 'Paused';
                await sleep(500);
                continue;
            }

            const kb = queue[currentIndex];
            const idx = currentIndex;

            // Highlight current row
            const rowElem = document.getElementById(`row-${idx}`);
            if (rowElem) rowElem.style.background = 'rgba(56, 189, 248, 0.1)';

            const statusElem = document.getElementById(`status-${idx}`);
            if (statusElem) statusElem.innerHTML = '<span class="badge-status badge-cyan"><i class="fa-solid fa-spinner fa-spin"></i> Loading</span>';

            try {
                const formData = new FormData();
                formData.append('action', actionType.value);
                formData.append('kodebooking', kb);
                formData.append('customwaktu', customWaktu.value);

                const res = await fetch('test_batch_worker.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();
                executionResults[idx] = data;

                // Update Row Info
                document.getElementById(`name-${idx}`).innerText = data.nm_pasien || 'Pasien';
                document.getElementById(`poli-${idx}`).innerText = data.nm_poli || '-';

                let isOk = false;
                let displayMsg = '';

                if (actionType.value === 'autofull') {
                    isOk = data.overall_success;
                    displayMsg = isOk ? 'Task 1..7 Berhasil Di-update ke BPJS' : 'Sebagian Task Gagal';
                } else if (actionType.value === 'check') {
                    isOk = data.found_in_db;
                    displayMsg = data.bpjs_response?.message || 'Checked';
                } else {
                    const code = data.bpjs_response?.data?.metadata?.code || data.bpjs_response?.http_code;
                    const msg  = data.bpjs_response?.data?.metadata?.message || 'Done';
                    isOk = (code == 200 || code == 208 || msg.toLowerCase() === 'ok');
                    displayMsg = `Code: ${code} - ${msg}`;
                }

                if (isOk) {
                    successCount++;
                    statusElem.innerHTML = '<span class="badge-status badge-success">SUKSES</span>';
                } else {
                    dangerCount++;
                    statusElem.innerHTML = '<span class="badge-status badge-danger">GAGAL / RESP</span>';
                }

                document.getElementById(`msg-${idx}`).innerText = displayMsg;
                document.getElementById(`action-${idx}`).innerHTML = `<button class="btn btn-primary" style="padding: 4px 10px; font-size: 11px;" onclick="viewDetail(${idx})">🔍 JSON</button>`;

            } catch (err) {
                dangerCount++;
                if (statusElem) statusElem.innerHTML = '<span class="badge-status badge-danger">ERROR</span>';
                document.getElementById(`msg-${idx}`).innerText = `JS Error: ${err.message}`;
            }

            if (rowElem) rowElem.style.background = 'transparent';

            currentIndex++;
            updateStatsUI();

            const delay = parseInt(delayMsInput.value) || 300;
            await sleep(delay);
        }

        // Complete Execution
        isRunning = false;
        btnStart.disabled = false;
        btnPause.disabled = true;
        btnStop.disabled = true;
        inputBookingList.disabled = false;
        actionType.disabled = false;

        if (stopRequested) {
            batchStateBadge.className = 'badge-status badge-amber';
            batchStateBadge.innerText = 'Dihentikan';
        } else {
            batchStateBadge.className = 'badge-status badge-success';
            batchStateBadge.innerText = 'Selesai 100%';
        }
    });

    btnPause.addEventListener('click', () => {
        if (!isRunning) return;
        isPaused = !isPaused;
        if (isPaused) {
            btnPause.innerHTML = '<i class="fa-solid fa-play"></i> <span>Resume</span>';
            batchStateBadge.className = 'badge-status badge-amber';
            batchStateBadge.innerText = 'Diberhentikan Sementara';
        } else {
            btnPause.innerHTML = '<i class="fa-solid fa-pause"></i> <span>Pause</span>';
            batchStateBadge.className = 'badge-status badge-cyan';
            batchStateBadge.innerText = 'Memproses...';
        }
    });

    btnStop.addEventListener('click', () => {
        if (!isRunning) return;
        if (confirm('Yakin ingin menghentikan proses batch?')) {
            stopRequested = true;
            isPaused = false;
        }
    });

    function viewDetail(idx) {
        const item = executionResults[idx];
        if (!item) return;
        modalTitle.innerText = `🔍 Respon JSON: ${item.kodebooking || ''} (${item.nm_pasien || ''})`;
        modalBodyCode.innerText = JSON.stringify(item, null, 2);
        modalInspector.style.display = 'flex';
    }

    function closeModal() {
        modalInspector.style.display = 'none';
    }
</script>
</body>
</html>
