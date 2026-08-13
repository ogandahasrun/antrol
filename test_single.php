<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester Single Booking - SIMKES Khanza Mobile JKN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0b0f17;
            --bg-card: rgba(22, 30, 46, 0.75);
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
            max-width: 1200px;
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

        .brand-title { display: flex; align-items: center; gap: 16px; }

        .brand-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--primary));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 22px;
            box-shadow: 0 4px 14px rgba(6, 182, 212, 0.4);
        }

        .brand-text h1 {
            font-size: 20px; font-weight: 700;
            background: linear-gradient(to right, #ffffff, #a5f3fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .brand-text p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-card);
            color: var(--text-main);
            padding: 10px 18px; border-radius: 10px;
            text-decoration: none; font-size: 13px; font-weight: 600;
            transition: all 0.2s ease;
        }
        .nav-btn:hover { background: rgba(59, 130, 246, 0.2); border-color: var(--primary); }

        /* Form Card */
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 24px;
            display: flex; flex-direction: column; gap: 20px;
        }

        .card-title { font-size: 16px; font-weight: 600; border-bottom: 1px solid var(--border-card); padding-bottom: 12px; }

        .grid-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 13px; color: var(--text-muted); font-weight: 500; }

        .form-control {
            background: rgba(11, 15, 23, 0.6);
            border: 1px solid var(--border-card);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-main);
            font-family: var(--font-main);
            font-size: 14px;
        }

        .form-control:focus { outline: none; border-color: var(--primary); }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #ffffff; border: none; border-radius: 10px;
            padding: 12px 24px; font-size: 14px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }

        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }

        /* Output Result Box */
        .result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 840px) { .result-grid { grid-template-columns: 1fr; } }

        .code-box {
            background: #0d1117;
            border: 1px solid var(--border-card);
            border-radius: 12px;
            padding: 16px;
            font-family: var(--font-code);
            font-size: 12.5px;
            color: #e5e7eb;
            overflow-x: auto;
            white-space: pre-wrap;
            max-height: 400px;
        }

        .box-title {
            font-size: 13px; font-weight: 600; color: var(--accent-cyan);
            margin-bottom: 10px; display: flex; align-items: center; gap: 8px;
        }

        .badge-status {
            padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;
        }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: var(--success); }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: var(--danger); }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <header>
        <div class="brand-title">
            <div class="brand-icon">🧪</div>
            <div class="brand-text">
                <h1>Tester Single Booking Mobile JKN</h1>
                <p>Uji Coba Pengiriman 1 Kode Booking / Task ID ke Web Service BPJS</p>
            </div>
        </div>
        <a href="index.php" class="nav-btn">📊 Kembali ke Dashboard Main</a>
    </header>

    <!-- Form Input Card -->
    <div class="card">
        <div class="card-title">📝 Parameter Uji Coba Request</div>

        <form id="formTest">
            <div class="grid-form">
                <div class="form-group">
                    <label for="actionType">Tipe Aksi Request:</label>
                    <select id="actionType" class="form-control">
                        <option value="add">1. Tambah Antrean (/antrean/add)</option>
                        <option value="task1">2. Task ID 1 - Mulai Tunggu Poli</option>
                        <option value="task2">3. Task ID 2 - Mulai Pelayanan Dokter</option>
                        <option value="task3">4. Task ID 3 - Selesai Pelayanan Dokter</option>
                        <option value="task4">5. Task ID 4 - Mulai Tunggu Farmasi</option>
                        <option value="task5">6. Task ID 5 - Mulai Racik Obat</option>
                        <option value="task6">7. Task ID 6 - Selesai Racik Obat</option>
                        <option value="task7">8. Task ID 7 - Penyerahan Obat</option>
                        <option value="task99">9. Task ID 99 - Batal Antrean</option>
                        <option value="batal">10. Pembatalan Antrean (/antrean/batal)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kodeBooking">Kode Booking / No Booking:</label>
                    <input type="text" id="kodeBooking" class="form-control" placeholder="Contoh: 202608120001 / 2026/08/12/000001" required>
                </div>

                <div class="form-group">
                    <label for="noRawat">No. Rawat (Opsional jika sama):</label>
                    <input type="text" id="noRawat" class="form-control" placeholder="Contoh: 2026/08/12/000001">
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" id="btnSubmit" class="btn">
                    <span>🚀 Kirim Request ke BPJS</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Output Response Card -->
    <div class="card" id="cardResult" style="display: none;">
        <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span>🔍 Hasil Eksekusi & Respon Web Service BPJS</span>
            <span id="statusBadge" class="badge-status"></span>
        </div>

        <div class="result-grid">
            <div>
                <div class="box-title">📤 Payload & Header JSON yang Dikirim</div>
                <div id="boxRequest" class="code-box">Belum ada request dikirim.</div>
            </div>
            <div>
                <div class="box-title">📥 Respon Diterima dari BPJS</div>
                <div id="boxResponse" class="code-box">Belum ada respon.</div>
            </div>
        </div>
    </div>
</div>

<script>
    const formTest = document.getElementById('formTest');
    const btnSubmit = document.getElementById('btnSubmit');
    const cardResult = document.getElementById('cardResult');
    const boxRequest = document.getElementById('boxRequest');
    const boxResponse = document.getElementById('boxResponse');
    const statusBadge = document.getElementById('statusBadge');

    formTest.addEventListener('submit', async (e) => {
        e.preventDefault();

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span>⏳ Memproses Request...</span>';
        cardResult.style.display = 'block';
        boxRequest.innerText = 'Menyiapkan payload...';
        boxResponse.innerText = 'Mengirim request ke server BPJS...';

        const actionType = document.getElementById('actionType').value;
        const kodeBooking = document.getElementById('kodeBooking').value.trim();
        const noRawat = document.getElementById('noRawat').value.trim() || kodeBooking;

        try {
            const formData = new FormData();
            formData.append('action', actionType);
            formData.append('kodebooking', kodeBooking);
            formData.append('norawat', noRawat);

            const res = await fetch('test_single_worker.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            boxRequest.innerText = JSON.stringify(data.request_payload || {}, null, 2);
            boxResponse.innerText = JSON.stringify(data.bpjs_response || {}, null, 2);

            const code = data.bpjs_response?.data?.metadata?.code || data.bpjs_response?.http_code;
            const msg = data.bpjs_response?.data?.metadata?.message || 'Done';

            if (code == 200 || code == 208 || msg.toLowerCase() === 'ok') {
                statusBadge.className = 'badge-status badge-success';
                statusBadge.innerText = `SUKSES (Code: ${code})`;
            } else {
                statusBadge.className = 'badge-status badge-danger';
                statusBadge.innerText = `RESPON BPJS (Code: ${code})`;
            }

        } catch (err) {
            boxResponse.innerText = `Error Javascript / Server: ${err.message}`;
            statusBadge.className = 'badge-status badge-danger';
            statusBadge.innerText = 'SYSTEM ERROR';
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<span>🚀 Kirim Request ke BPJS</span>';
        }
    });
</script>
</body>
</html>
