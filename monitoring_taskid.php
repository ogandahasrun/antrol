<?php require_once __DIR__ . '/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring & Kontrol Task ID - SIMKES Khanza Antrol</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-blue: #38bdf8;
            --accent-green: #22c55e;
            --accent-yellow: #eab308;
            --accent-red: #ef4444;
            --accent-purple: #a855f7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 98%;
            margin: 0 auto;
        }

        /* Table Area */
        .table-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .table-responsive {
            width: 100%;
            max-height: calc(100vh - 260px);
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            text-align: left;
        }

        thead {
            background: #0f172a;
        }

        th {
            position: sticky;
            top: 0;
            background: #0f172a;
            padding: 14px 16px;
            font-weight: 600;
            color: var(--accent-blue);
            white-space: nowrap;
            border-bottom: 2px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            padding: 20px 28px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .header-title h1 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.15);
            color: var(--accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-nav:hover {
            background: rgba(56, 189, 248, 0.3);
            transform: translateY(-2px);
        }

        /* Summary Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .metric-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .card-blue::before { background: var(--accent-blue); }
        .card-green::before { background: var(--accent-green); }
        .card-yellow::before { background: var(--accent-yellow); }
        .card-purple::before { background: var(--accent-purple); }

        .metric-card h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .metric-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Filter Controls */
        .filter-bar {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            padding: 18px 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-blue);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-sync-all {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        td {
            padding: 12px 16px;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Task Time Badges */
        .task-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-family: monospace;
            font-weight: 500;
        }

        .task-done {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .task-missing {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .task-na {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-lengkap {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .status-belum {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border: 1px solid rgba(234, 179, 8, 0.4);
        }

        .btn-retry-single {
            background: rgba(56, 189, 248, 0.1);
            color: var(--accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.2s ease;
        }

        .btn-retry-single:hover {
            background: rgba(56, 189, 248, 0.3);
        }

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

        .loading-overlay {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="header-title">
                <h1 style="display:flex; align-items:center; gap:12px; font-size:20px; font-weight:700;"><i class="fa-solid fa-chart-line"></i> Kontrol & Monitoring Task ID</h1>
                <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Matriks audit kelengkapan waktu pelayanan (Task 1 s.d. 7) pasien BPJS & Onsite</p>
            </div>
            <nav class="nav-links">
                <a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i> Home</a>
                <a href="engine_sync.php" class="nav-item"><i class="fa-solid fa-bolt"></i> Engine Sync</a>
                <a href="monitoring_taskid.php" class="nav-item active"><i class="fa-solid fa-chart-line"></i> Kontrol Task ID</a>
                <a href="test_single.php" class="nav-item"><i class="fa-solid fa-paper-plane"></i> Kirim Single</a>
                <a href="test_batch.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Kirim Bulk</a>
                <a href="dashboard_waktutunggu.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard BPJS</a>
            </nav>
        </header>

        <!-- Metrics Grid -->
        <div class="metrics-grid">
            <div class="metric-card card-blue">
                <h3>Total Pasien</h3>
                <div class="value" id="metricTotal">0</div>
            </div>
            <div class="metric-card card-green">
                <h3>Sudah Lengkap</h3>
                <div class="value" id="metricLengkap">0</div>
            </div>
            <div class="metric-card card-yellow">
                <h3>Belum Lengkap</h3>
                <div class="value" id="metricBelum">0</div>
            </div>
            <div class="metric-card card-purple">
                <h3>Pasien Ber-Resep</h3>
                <div class="value" id="metricResep">0</div>
            </div>
        </div>

        <!-- Filter Control Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label style="font-size: 13px; color: var(--text-muted);"><i class="fa-regular fa-calendar"></i> Periode:</label>
                <input type="date" id="tgl1" class="form-control" value="<?= date('Y-m-d') ?>">
                <span style="color: var(--text-muted);">s.d.</span>
                <input type="date" id="tgl2" class="form-control" value="<?= date('Y-m-d') ?>">

                <label style="font-size: 13px; color: var(--text-muted); margin-left: 12px;"><i class="fa-solid fa-filter"></i> Status:</label>
                <select id="filterStatus" class="form-control">
                    <option value="semua">Semua Pasien</option>
                    <option value="lengkap">Sudah Lengkap (Hijau)</option>
                    <option value="belum">Belum Lengkap (Kuning)</option>
                </select>
                
                <button id="btnLoad" class="btn-action"><i class="fa-solid fa-magnifying-glass"></i> Tampilkan</button>
            </div>

            <div class="filter-group">
                <input type="text" id="searchKeyword" class="form-control" placeholder="Cari Nama / No RM / No Rawat / Poli..." style="width: 260px;">
                <button id="btnSyncAll" class="btn-action btn-sync-all"><i class="fa-solid fa-rotate"></i> Sync Engine</button>
            </div>
        </div>

        <!-- Table Area -->
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>No Booking / Rawat</th>
                            <th>No RM</th>
                            <th>Nama Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter</th>
                            <th>Task 1</th>
                            <th>Task 2</th>
                            <th>Task 3 *</th>
                            <th>Task 4 *</th>
                            <th>Task 5 *</th>
                            <th>Task 6</th>
                            <th>Task 7</th>
                            <th style="text-align: center;">Status Kirim</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="14" class="loading-overlay">
                                <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                                <p style="margin-top: 8px;">Memuat data monitoring...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript Client Logic -->
    <script>
        let globalData = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadData();

            document.getElementById('btnLoad').addEventListener('click', loadData);
            document.getElementById('searchKeyword').addEventListener('input', renderTable);
            document.getElementById('filterStatus').addEventListener('change', renderTable);
            document.getElementById('btnSyncAll').addEventListener('click', runSyncEngine);
        });

        async function loadData() {
            const tgl1 = document.getElementById('tgl1').value;
            const tgl2 = document.getElementById('tgl2').value;
            const status = document.getElementById('filterStatus').value;

            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="14" class="loading-overlay">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top: 8px;">Memuat data monitoring tanggal ${tgl1} s.d. ${tgl2}...</p>
                    </td>
                </tr>
            `;

            try {
                const response = await fetch(`api_monitoring_taskid.php?tanggal1=${tgl1}&tanggal2=${tgl2}&status=${status}`);
                const result = await response.json();

                if (result.status) {
                    globalData = result.data || [];
                    
                    // Update Summary Cards
                    document.getElementById('metricTotal').innerText = result.summary.total_pasien;
                    document.getElementById('metricLengkap').innerText = result.summary.sudah_lengkap;
                    document.getElementById('metricBelum').innerText = result.summary.belum_lengkap;
                    document.getElementById('metricResep').innerText = result.summary.ber_resep;

                    renderTable();
                } else {
                    tbody.innerHTML = `<tr><td colspan="14" style="text-align:center; color:#f87171; padding:20px;">Gagal memuat data</td></tr>`;
                }
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="14" style="text-align:center; color:#f87171; padding:20px;">Error: ${err.message}</td></tr>`;
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            const keyword = document.getElementById('searchKeyword').value.toLowerCase();
            const filterStatus = document.getElementById('filterStatus').value;

            const filtered = globalData.filter(item => {
                const matchKeyword = item.nm_pasien.toLowerCase().includes(keyword) ||
                                     item.no_rawat.toLowerCase().includes(keyword) ||
                                     item.no_rkm_medis.toLowerCase().includes(keyword) ||
                                     item.nm_poli.toLowerCase().includes(keyword);
                
                const matchStatus = (filterStatus === 'semua') ||
                                    (filterStatus === 'lengkap' && item.is_lengkap) ||
                                    (filterStatus === 'belum' && !item.is_lengkap);

                return matchKeyword && matchStatus;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="14" style="text-align:center; color:var(--text-muted); padding:30px;">Tidak ada data pasien yang sesuai kriteria</td></tr>`;
                return;
            }

            let html = '';
            filtered.forEach((row, idx) => {
                html += `
                    <tr>
                        <td style="color: var(--text-muted); font-weight:500;">${idx + 1}</td>
                        <td style="font-family: monospace; font-weight:600; color:var(--accent-blue);">${row.no_rawat}</td>
                        <td style="font-family: monospace;">${row.no_rkm_medis}</td>
                        <td style="font-weight:600;">${row.nm_pasien}</td>
                        <td>${row.nm_poli}</td>
                        <td style="color: var(--text-muted);">${row.nm_dokter}</td>
                        <td>${formatTaskBadge(row.task1)}</td>
                        <td>${formatTaskBadge(row.task2)}</td>
                        <td>${formatTaskBadge(row.task3)}</td>
                        <td>${formatTaskBadge(row.task4)}</td>
                        <td>${formatTaskBadge(row.task5)}</td>
                        <td>${formatTaskBadge(row.task6)}</td>
                        <td>${formatTaskBadge(row.task7)}</td>
                        <td style="text-align: center;">
                            <span class="status-badge ${row.is_lengkap ? 'status-lengkap' : 'status-belum'}">
                                <i class="fa-solid ${row.is_lengkap ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> ${row.status_kirim}
                            </span>
                            <div style="margin-top: 4px;">
                                <button class="btn-retry-single" onclick="retrySingle('${row.no_rawat}')"><i class="fa-solid fa-paper-plane"></i> Sync</button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function formatTaskBadge(val) {
            if (!val || val === '-') {
                return `<span class="task-badge task-missing"><i class="fa-solid fa-xmark"></i> -</span>`;
            }
            if (val.startsWith('N/A')) {
                return `<span class="task-badge task-na"><i class="fa-solid fa-ban"></i> Tanpa Resep</span>`;
            }
            // Cut string date time to format HH:mm:ss if same date or short format
            const shortTime = val.length >= 19 ? val.substring(11, 19) : val;
            return `<span class="task-badge task-done" title="${val}"><i class="fa-solid fa-check"></i> ${shortTime}</span>`;
        }

        async function retrySingle(noRawat) {
            alert(`Memicu pemicuan sync ulang untuk No. Rawat: ${noRawat}`);
            loadData();
        }

        async function runSyncEngine() {
            const btn = document.getElementById('btnSyncAll');
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Memproses Engine...`;
            btn.disabled = true;

            const tgl1 = document.getElementById('tgl1').value;
            const tgl2 = document.getElementById('tgl2').value;

            try {
                const res = await fetch(`service_antrol.php?tanggal1=${tgl1}&tanggal2=${tgl2}`);
                const data = await res.json();
                alert(`Sync Selesai!\nStatus: ${data.status ? 'Berhasil' : 'Gagal'}\nLogs: ${data.logs ? data.logs.slice(-3).join('\n') : ''}`);
                loadData();
            } catch (err) {
                alert(`Error Sync: ${err.message}`);
            } finally {
                btn.innerHTML = `<i class="fa-solid fa-rotate"></i> Sync Engine`;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
