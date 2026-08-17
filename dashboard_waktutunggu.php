<?php require_once __DIR__ . '/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Waktu Tunggu BPJS - SIMKES Khanza Antrol</title>
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
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(168, 85, 247, 0.4);
            font-weight: 700; font-size: 20px;
        }

        .brand-text h1 {
            font-size: 19px; font-weight: 700;
            background: linear-gradient(90deg, #ffffff, #c084fc);
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
            color: #ffffff; background: rgba(168, 85, 247, 0.2);
            border-color: rgba(168, 85, 247, 0.4); font-weight: 600;
        }

        /* Filter Card */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; border-bottom: 1px solid var(--card-border); padding-bottom: 14px;
        }

        .card-title {
            font-size: 16px; font-weight: 600; color: #ffffff;
            display: flex; align-items: center; gap: 10px;
        }

        .filter-form {
            display: flex; flex-wrap: wrap; items-center; gap: 16px;
        }

        .form-group {
            display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 200px;
        }

        .form-group label {
            font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(11, 15, 23, 0.7);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--text-main);
            font-family: var(--font-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.25);
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-purple), #7e22ce);
            color: #ffffff; border: none; border-radius: 10px;
            padding: 12px 24px; font-size: 14px; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(168, 85, 247, 0.35);
            align-self: flex-end;
            height: 43px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.5);
        }

        /* Metric Grid Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .metric-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 20px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .metric-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }

        .metric-info h4 { font-size: 12px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .metric-info p { font-size: 22px; font-weight: 700; color: #ffffff; margin-top: 2px; }

        /* Table Card */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--card-border);
        }

        table {
            width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;
        }

        thead { background: rgba(15, 23, 42, 0.95); }

        th {
            padding: 14px 16px; font-weight: 600; color: var(--accent-blue);
            border-bottom: 2px solid var(--card-border); white-space: nowrap;
        }

        td {
            padding: 13px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); color: var(--text-main); white-space: nowrap;
        }

        tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

        .task-badge {
            display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; font-family: var(--font-code);
            background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .badge-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; border-color: rgba(168, 85, 247, 0.3); }
        .badge-cyan { background: rgba(6, 182, 212, 0.15); color: #67e8f9; border-color: rgba(6, 182, 212, 0.3); }
        .badge-amber { background: rgba(245, 158, 11, 0.15); color: #fde047; border-color: rgba(245, 158, 11, 0.3); }
        .badge-green { background: rgba(34, 197, 94, 0.15); color: #4ade80; border-color: rgba(34, 197, 94, 0.3); }

        /* Code Box */
        .code-box {
            background: #0d1117;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 16px;
            font-family: var(--font-code);
            font-size: 12px;
            color: #e5e7eb;
            overflow-x: auto;
            white-space: pre-wrap;
            max-height: 350px;
        }

        .endpoint-pill {
            font-family: var(--font-code);
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(56, 189, 248, 0.15);
            color: var(--accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon"><i class="fa-solid fa-chart-pie"></i></div>
            <div class="brand-text">
                <h1>Dashboard Waktu Tunggu BPJS</h1>
                <p>Monitoring Rekapitulasi Rerata Waktu Tunggu Server BPJS Kesehatan</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i> Home</a>
            <a href="engine_sync.php" class="nav-item"><i class="fa-solid fa-bolt"></i> Engine Sync</a>
            <a href="monitoring_taskid.php" class="nav-item"><i class="fa-solid fa-chart-line"></i> Kontrol Task ID</a>
            <a href="test_single.php" class="nav-item"><i class="fa-solid fa-paper-plane"></i> Kirim Single</a>
            <a href="test_batch.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Kirim Bulk</a>
            <a href="dashboard_waktutunggu.php" class="nav-item active"><i class="fa-solid fa-chart-pie"></i> Dashboard BPJS</a>
        </nav>
    </header>

    <!-- Filter Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-filter" style="color: var(--accent-purple);"></i>
                <span>Filter Parameter Request BPJS</span>
            </div>
            <span id="endpointLabel" class="endpoint-pill">GET /dashboard/waktutunggu/tanggal/--/waktu/rs</span>
        </div>

        <form id="filterForm" class="filter-form">
            <div class="form-group">
                <label for="tanggalInput"><i class="fa-regular fa-calendar"></i> Tanggal Periksa</label>
                <input type="date" id="tanggalInput" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="waktuSelect"><label><i class="fa-solid fa-clock"></i> Tipe Acuan Waktu BPJS</label>
                <select id="waktuSelect" class="form-control">
                    <option value="rs" selected>RS (Waktu Lokal Faskes / SIMRS)</option>
                    <option value="server">SERVER (Waktu Sistem Server BPJS)</option>
                </select>
            </div>

            <button type="submit" id="btnFetch" class="btn">
                <i class="fa-solid fa-rotate"></i> <span>Tampilkan Data BPJS</span>
            </button>
        </form>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(168, 85, 247, 0.2); color: #c084fc;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div class="metric-info">
                <h4>Total Poliklinik</h4>
                <p id="metricTotalPoli">0</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(6, 182, 212, 0.2); color: #67e8f9;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="metric-info">
                <h4>Total Antrean BPJS</h4>
                <p id="metricTotalAntrean">0</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(56, 189, 248, 0.2); color: #7dd3fc;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div class="metric-info">
                <h4>Rerata Pelayanan Dokter</h4>
                <p id="metricAvgDokter">00:00:00</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;">
                <i class="fa-solid fa-pills"></i>
            </div>
            <div class="metric-info">
                <h4>Rerata Racik Obat</h4>
                <p id="metricAvgFarmasi">00:00:00</p>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-list-check" style="color: var(--accent-cyan);"></i>
                <span>Rincian Waktu Tunggu Per Poliklinik (Data Resmi BPJS)</span>
            </div>
            <span id="statusBadge" class="task-badge badge-purple">Ready</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Poli</th>
                        <th>Nama Poliklinik</th>
                        <th>Jml Antrean</th>
                        <th>Task 1 (Tunggu Poli)</th>
                        <th>Task 2 (Mulai Loket)</th>
                        <th>Task 3 (Validasi)</th>
                        <th>Task 4 (Mulai Dokter)</th>
                        <th>Task 5 (Selesai Dokter)</th>
                        <th>Task 6 (Selesai Obat)</th>
                        <th>Task 7 (Serah Obat)</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Silakan klik <b>"Tampilkan Data BPJS"</b> untuk memuat data.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Raw JSON Inspector Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-code" style="color: var(--accent-amber);"></i>
                <span>Inspeksi Raw JSON Payload Respon BPJS</span>
            </div>
        </div>
        <div id="codeInspector" class="code-box">Belum ada request dikirim.</div>
    </div>
</div>

<script>
    const filterForm = document.getElementById('filterForm');
    const tanggalInput = document.getElementById('tanggalInput');
    const waktuSelect = document.getElementById('waktuSelect');
    const btnFetch = document.getElementById('btnFetch');
    const endpointLabel = document.getElementById('endpointLabel');
    const statusBadge = document.getElementById('statusBadge');
    const tableBody = document.getElementById('tableBody');
    const codeInspector = document.getElementById('codeInspector');

    const metricTotalPoli = document.getElementById('metricTotalPoli');
    const metricTotalAntrean = document.getElementById('metricTotalAntrean');
    const metricAvgDokter = document.getElementById('metricAvgDokter');
    const metricAvgFarmasi = document.getElementById('metricAvgFarmasi');

    function formatSecs(secs) {
        if (!secs || isNaN(secs)) return '00:00:00';
        const h = Math.floor(secs / 3600).toString().padStart(2, '0');
        const m = Math.floor((secs % 3600) / 60).toString().padStart(2, '0');
        const s = Math.floor(secs % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    async function loadData() {
        const tgl = tanggalInput.value;
        const waktu = waktuSelect.value;
        const endpoint = `/dashboard/waktutunggu/tanggal/${tgl}/waktu/${waktu}`;
        endpointLabel.innerText = `GET ${endpoint}`;

        btnFetch.disabled = true;
        btnFetch.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memuat BPJS...</span>';
        statusBadge.className = 'task-badge badge-amber';
        statusBadge.innerText = 'Mengirim Request...';
        codeInspector.innerText = 'Mengirim request ke BPJS...';

        try {
            const formData = new FormData();
            formData.append('tanggal', tgl);
            formData.append('waktu', waktu);

            const res = await fetch('dashboard_waktutunggu_worker.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            codeInspector.innerText = JSON.stringify(data, null, 2);

            if (data.status && data.decrypted) {
                let list = [];
                if (Array.isArray(data.decrypted)) {
                    list = data.decrypted;
                } else if (data.decrypted.list && Array.isArray(data.decrypted.list)) {
                    list = data.decrypted.list;
                }

                statusBadge.className = 'task-badge badge-green';
                statusBadge.innerText = `SUKSES (${list.length} Poli)`;

                metricTotalPoli.innerText = list.length;
                let totalAntrean = 0;
                let totalTask45 = 0;
                let totalTask67 = 0;
                let countTask45 = 0;
                let countTask67 = 0;

                if (list.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                Data tidak ditemukan atau belum ada antrean tercatat di BPJS untuk tanggal <b>${tgl}</b>.
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';
                    list.forEach((item, idx) => {
                        const jml = parseInt(item.jumlah_antrean || 0);
                        totalAntrean += jml;

                        const t4 = parseInt(item.waktu_task4 || 0);
                        const t5 = parseInt(item.waktu_task5 || 0);
                        const t6 = parseInt(item.waktu_task6 || 0);
                        const t7 = parseInt(item.waktu_task7 || 0);

                        if (t4 > 0 || t5 > 0) {
                            totalTask45 += (t4 + t5);
                            countTask45++;
                        }
                        if (t6 > 0 || t7 > 0) {
                            totalTask67 += (t6 + t7);
                            countTask67++;
                        }

                        html += `
                            <tr>
                                <td>${idx + 1}</td>
                                <td><span class="task-badge badge-cyan">${item.kodepoli || '-'}</span></td>
                                <td><b>${item.namapoli || '-'}</b></td>
                                <td><span class="task-badge badge-green">${jml}</span></td>
                                <td>${item.avg_waktu_task1 || formatSecs(item.waktu_task1)}</td>
                                <td>${item.avg_waktu_task2 || formatSecs(item.waktu_task2)}</td>
                                <td>${item.avg_waktu_task3 || formatSecs(item.waktu_task3)}</td>
                                <td>${item.avg_waktu_task4 || formatSecs(item.waktu_task4)}</td>
                                <td>${item.avg_waktu_task5 || formatSecs(item.waktu_task5)}</td>
                                <td>${item.avg_waktu_task6 || formatSecs(item.waktu_task6)}</td>
                                <td>${item.avg_waktu_task7 || formatSecs(item.waktu_task7)}</td>
                            </tr>
                        `;
                    });

                    tableBody.innerHTML = html;
                }

                metricTotalAntrean.innerText = totalAntrean;
                metricAvgDokter.innerText = countTask45 > 0 ? formatSecs(Math.round(totalTask45 / countTask45)) : '00:00:00';
                metricAvgFarmasi.innerText = countTask67 > 0 ? formatSecs(Math.round(totalTask67 / countTask67)) : '00:00:00';

            } else {
                const msg = data.metadata?.message || data.message || 'Gagal mengambil data BPJS';
                const code = data.metadata?.code || 500;
                statusBadge.className = 'task-badge badge-amber';
                statusBadge.innerText = `RESPON: ${msg} (${code})`;

                tableBody.innerHTML = `
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--accent-red); padding: 30px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Respon BPJS: <b>${msg}</b> (Code: ${code})
                        </td>
                    </tr>
                `;
            }
        } catch (err) {
            statusBadge.className = 'task-badge badge-amber';
            statusBadge.innerText = 'SYSTEM ERROR';
            codeInspector.innerText = `Error Javascript: ${err.message}`;
            tableBody.innerHTML = `
                <tr>
                    <td colspan="11" style="text-align: center; color: var(--accent-red); padding: 30px;">
                        Gagal melakukan request: ${err.message}
                    </td>
                </tr>
            `;
        } finally {
            btnFetch.disabled = false;
            btnFetch.innerHTML = '<i class="fa-solid fa-rotate"></i> <span>Tampilkan Data BPJS</span>';
        }
    }

    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        loadData();
    });

    // Automatis load data saat pertama dibuka
    loadData();
</script>
</body>
</html>
