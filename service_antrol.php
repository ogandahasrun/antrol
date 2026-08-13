<?php
/**
 * Service Worker Engine Bridging Mobile JKN BPJS (PHP Native)
 * Meniru secara 100% logika eksekusi dari Java JAR (frmUtama.java)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/bpjs_helper.php';

// Menangkap parameter Tanggal1 dan Tanggal2 (Default: Hari ini)
$tanggal1 = isset($_REQUEST['tanggal1']) && !empty($_REQUEST['tanggal1']) ? $_REQUEST['tanggal1'] : date('Y-m-d');
$tanggal2 = isset($_REQUEST['tanggal2']) && !empty($_REQUEST['tanggal2']) ? $_REQUEST['tanggal2'] : date('Y-m-d');

// Buffer Log Output
$logs = [];
$stats = [
    'add_success' => 0,
    'batal_success' => 0,
    'taskid_success' => 0,
    'taskid_failed' => 0
];

function logMsg($msg) {
    global $logs;
    $timeStr = date('H:i:s');
    $logs[] = "[$timeStr] $msg";
}

logMsg("Starting SIMKES Khanza Service Mobile JKN Bridging (PHP Native)");
logMsg("Periode Tanggal: $tanggal1 s.d. $tanggal2");

// Inisialisasi Kredensial BPJS
BpjsHelper::initConfig();
logMsg("Base URL API: " . BpjsHelper::getBaseUrl());

// ----------------------------------------------------------------------------------
// 1. WEBSERVICE TAMBAH ANTREAN MOBILE JKN PASIEN BPJS (/antrean/add)
// ----------------------------------------------------------------------------------
logMsg("Menjalankan WS tambah antrian Mobile JKN Pasien BPJS...");

$sqlAdd = "SELECT 
            referensi_mobilejkn_bpjs.nobooking,
            referensi_mobilejkn_bpjs.no_rawat,
            reg_periksa.no_rkm_medis,
            pasien.nm_pasien,
            referensi_mobilejkn_bpjs.nohp,
            referensi_mobilejkn_bpjs.nomorkartu,
            referensi_mobilejkn_bpjs.nik,
            referensi_mobilejkn_bpjs.tanggalperiksa,
            poliklinik.nm_poli,
            dokter.nm_dokter,
            referensi_mobilejkn_bpjs.jampraktek,
            referensi_mobilejkn_bpjs.jeniskunjungan,
            referensi_mobilejkn_bpjs.nomorreferensi,
            referensi_mobilejkn_bpjs.status,
            referensi_mobilejkn_bpjs.validasi,
            referensi_mobilejkn_bpjs.kodepoli,
            referensi_mobilejkn_bpjs.pasienbaru,
            referensi_mobilejkn_bpjs.kodedokter,
            referensi_mobilejkn_bpjs.nomorantrean,
            referensi_mobilejkn_bpjs.angkaantrean,
            referensi_mobilejkn_bpjs.estimasidilayani,
            referensi_mobilejkn_bpjs.sisakuotajkn,
            referensi_mobilejkn_bpjs.kuotajkn,
            referensi_mobilejkn_bpjs.sisakuotanonjkn,
            referensi_mobilejkn_bpjs.kuotanonjkn 
          FROM referensi_mobilejkn_bpjs 
          INNER JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat=reg_periksa.no_rawat 
          INNER JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis 
          INNER JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli 
          INNER JOIN dokter ON reg_periksa.kd_dokter=dokter.kd_dokter 
          WHERE referensi_mobilejkn_bpjs.statuskirim='Belum' 
            AND referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl1 AND :tgl2 
          ORDER BY referensi_mobilejkn_bpjs.tanggalperiksa";

try {
    $stmtAdd = $pdo->prepare($sqlAdd);
    $stmtAdd->execute([':tgl1' => $tanggal1, ':tgl2' => $tanggal2]);
    $rowsAdd = $stmtAdd->fetchAll();

    foreach ($rowsAdd as $row) {
        $payloadAdd = [
            "kodebooking"     => $row['nobooking'],
            "jenispasien"     => "JKN",
            "nomorkartu"      => $row['nomorkartu'],
            "nik"             => $row['nik'],
            "nohp"            => $row['nohp'],
            "kodepoli"        => $row['kodepoli'],
            "namapoli"        => $row['nm_poli'],
            "pasienbaru"      => (int)$row['pasienbaru'],
            "norm"            => $row['no_rkm_medis'],
            "tanggalperiksa"  => $row['tanggalperiksa'],
            "kodedokter"      => (int)$row['kodedokter'],
            "namadokter"      => $row['nm_dokter'],
            "jampraktek"      => $row['jampraktek'],
            "jeniskunjungan"  => (int)substr($row['jeniskunjungan'], 0, 1),
            "nomorreferensi"  => $row['nomorreferensi'],
            "nomorantrean"    => $row['nomorantrean'],
            "angkaantrean"    => (int)$row['angkaantrean'],
            "estimasidilayani"=> (int)$row['estimasidilayani'],
            "sisakuotajkn"    => (int)$row['sisakuotajkn'],
            "kuotajkn"        => (int)$row['kuotajkn'],
            "sisakuotanonjkn" => (int)$row['sisakuotanonjkn'],
            "kuotanonjkn"     => (int)$row['kuotanonjkn'],
            "keterangan"      => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
        ];

        logMsg("JSON Add: " . json_encode($payloadAdd));
        $res = BpjsHelper::sendRequest("/antrean/add", $payloadAdd, 'POST');

        $code = isset($res['data']['metadata']['code']) ? (string)$res['data']['metadata']['code'] : '';
        $msg  = isset($res['data']['metadata']['message']) ? $res['data']['metadata']['message'] : '';

        logMsg("Respon WS BPJS: $code $msg");

        if ($code === '200' || $code === '208' || strtolower($msg) === 'ok') {
            $updateStmt = $pdo->prepare("UPDATE referensi_mobilejkn_bpjs SET statuskirim='Sudah' WHERE nobooking = :nobooking");
            $updateStmt->execute([':nobooking' => $row['nobooking']]);
            $stats['add_success']++;
        }
    }
} catch (Exception $e) {
    logMsg("Notifikasi Add Error: " . $e->getMessage());
}

// ----------------------------------------------------------------------------------
// 2. WEBSERVICE BATAL ANTREAN MOBILE JKN PASIEN BPJS (/antrean/batal)
// ----------------------------------------------------------------------------------
logMsg("Menjalankan WS batal antrian Mobile JKN Pasien BPJS...");

$sqlBatal = "SELECT * FROM referensi_mobilejkn_bpjs_batal 
             WHERE referensi_mobilejkn_bpjs_batal.statuskirim='Belum' 
               AND referensi_mobilejkn_bpjs_batal.tanggalbatal BETWEEN :tgl1 AND :tgl2";

try {
    $stmtBatal = $pdo->prepare($sqlBatal);
    $stmtBatal->execute([':tgl1' => $tanggal1, ':tgl2' => $tanggal2]);
    $rowsBatal = $stmtBatal->fetchAll();

    foreach ($rowsBatal as $row) {
        $payloadBatal = [
            "kodebooking" => $row['nobooking'],
            "keterangan"  => $row['keterangan']
        ];

        logMsg("JSON Batal: " . json_encode($payloadBatal));
        $res = BpjsHelper::sendRequest("/antrean/batal", $payloadBatal, 'POST');

        $code = isset($res['data']['metadata']['code']) ? (string)$res['data']['metadata']['code'] : '';
        $msg  = isset($res['data']['metadata']['message']) ? $res['data']['metadata']['message'] : '';

        logMsg("Respon WS BPJS: $code $msg");

        if ($code === '200') {
            $updBatal = $pdo->prepare("UPDATE referensi_mobilejkn_bpjs_batal SET statuskirim='Sudah' WHERE nomorreferensi = :nomorreferensi");
            $updBatal->execute([':nomorreferensi' => $row['nomorreferensi']]);
            $stats['batal_success']++;

            // Kirim Task ID 99 (Batal)
            $tglBatal = $row['tanggalbatal'];
            if (!empty($tglBatal)) {
                sendTaskIdUpdate($pdo, $row['nobooking'], $row['no_rawat'], '99', $tglBatal, "taskid batal pelayanan poli BPJS", $stats);
            }
        }
    }
} catch (Exception $e) {
    logMsg("Notifikasi Batal Error: " . $e->getMessage());
}

// ----------------------------------------------------------------------------------
// HELPER UTAMA UNTUK UPDATE TASK ID (BPJS & NON-BPJS)
// ----------------------------------------------------------------------------------
function sendTaskIdUpdate($pdo, $kodeBooking, $noRawat, $taskId, $waktuStr, $label, &$stats) {
    if (empty($waktuStr) || $waktuStr === '0000-00-00 00:00:00') return;

    // Simpan temporary record ke referensi_mobilejkn_bpjs_taskid
    try {
        $insStmt = $pdo->prepare("INSERT INTO referensi_mobilejkn_bpjs_taskid (no_rawat, taskid, waktu) VALUES (:no_rawat, :taskid, :waktu)");
        $insStmt->execute([':no_rawat' => $noRawat, ':taskid' => $taskId, ':waktu' => $waktuStr]);
    } catch (Exception $ex) {
        // Jika sudah ada record, lewati atau dapat di-retry
        return;
    }

    $epochMs = strtotime($waktuStr) * 1000;
    $payloadTask = [
        "kodebooking" => $kodeBooking,
        "taskid"      => (string)$taskId,
        "waktu"       => (string)$epochMs
    ];

    logMsg("Menjalankan WS $label (Task ID $taskId)");
    logMsg("JSON: " . json_encode($payloadTask));

    $res = BpjsHelper::sendRequest("/antrean/updatewaktu", $payloadTask, 'POST');
    $code = isset($res['data']['metadata']['code']) ? (string)$res['data']['metadata']['code'] : '';
    $msg  = isset($res['data']['metadata']['message']) ? $res['data']['metadata']['message'] : '';

    logMsg("Respon WS BPJS: $code $msg");

    if ($code === '200') {
        $stats['taskid_success']++;
    } else {
        // Hapus record dari database jika gagal agar dapat di-retry pada siklus berikutnya
        $delStmt = $pdo->prepare("DELETE FROM referensi_mobilejkn_bpjs_taskid WHERE taskid = :taskid AND no_rawat = :no_rawat");
        $delStmt->execute([':taskid' => $taskId, ':no_rawat' => $noRawat]);
        $stats['taskid_failed']++;
    }
}

// ----------------------------------------------------------------------------------
// 3. PROCESS TASK ID 1 s.d. 7 (PASIEN BPJS)
// ----------------------------------------------------------------------------------
logMsg("Memproses Task ID 1-7 Pasien BPJS...");

$sqlBpjsTask = "SELECT 
                  referensi_mobilejkn_bpjs.nobooking,
                  referensi_mobilejkn_bpjs.no_rawat,
                  referensi_mobilejkn_bpjs.validasi 
                FROM referensi_mobilejkn_bpjs 
                WHERE referensi_mobilejkn_bpjs.statuskirim='Sudah' 
                  AND referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl1 AND :tgl2 
                ORDER BY referensi_mobilejkn_bpjs.tanggalperiksa";

try {
    $stmtBpjs = $pdo->prepare($sqlBpjsTask);
    $stmtBpjs->execute([':tgl1' => $tanggal1, ':tgl2' => $tanggal2]);
    $rowsBpjs = $stmtBpjs->fetchAll();

    foreach ($rowsBpjs as $row) {
        $noRawat = $row['no_rawat'];
        $noBooking = $row['nobooking'];

        // Digit ke-14 no_rawat untuk modulus
        $digit14 = strlen($noRawat) >= 14 ? (int)substr($noRawat, 13, 1) : 0;

        // TASK ID 1 (Mulai Tunggu Poli: 37 + (modulus % 7) menit sebelum validasi)
        $mod1 = $digit14 % 7;
        $qT1 = $pdo->prepare("SELECT SUBDATE(validasi, INTERVAL " . (37 + $mod1) . " MINUTE) AS jam FROM referensi_mobilejkn_bpjs WHERE no_rawat = :no_rawat");
        $qT1->execute([':no_rawat' => $noRawat]);
        $t1Time = $qT1->fetchColumn();
        if ($t1Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '1', $t1Time, "taskid mulai tunggu poli BPJS", $stats);

        // TASK ID 2 (Mulai Pelayanan Dokter Poli: 18 + (modulus % 4) menit sebelum validasi)
        $mod2 = $digit14 % 4;
        $qT2 = $pdo->prepare("SELECT SUBDATE(validasi, INTERVAL " . (18 + $mod2) . " MINUTE) AS jam FROM referensi_mobilejkn_bpjs WHERE no_rawat = :no_rawat");
        $qT2->execute([':no_rawat' => $noRawat]);
        $t2Time = $qT2->fetchColumn();
        if ($t2Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '2', $t2Time, "taskid mulai pelayanan poli BPJS", $stats);

        // TASK ID 3 (Selesai Pelayanan Dokter Poli: Tepat waktu validasi)
        $t3Time = $row['validasi'];
        if ($t3Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '3', $t3Time, "taskid selesai pelayanan poli BPJS", $stats);

        // TASK ID 4 (Mulai Tunggu Farmasi: 12 + (modulus % 3) menit setelah validasi)
        $mod4 = $digit14 % 3;
        $qT4 = $pdo->prepare("SELECT DATE_ADD(validasi, INTERVAL " . (12 + $mod4) . " MINUTE) AS jam FROM referensi_mobilejkn_bpjs WHERE no_rawat = :no_rawat");
        $qT4->execute([':no_rawat' => $noRawat]);
        $t4Time = $qT4->fetchColumn();
        if ($t4Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '4', $t4Time, "taskid mulai tunggu farmasi BPJS", $stats);

        // TASK ID 5 (Mulai Racik Obat: 31 + (modulus % 6) menit setelah validasi)
        $mod5 = $digit14 % 6;
        $qT5 = $pdo->prepare("SELECT DATE_ADD(validasi, INTERVAL " . (31 + $mod5) . " MINUTE) AS jam FROM referensi_mobilejkn_bpjs WHERE no_rawat = :no_rawat");
        $qT5->execute([':no_rawat' => $noRawat]);
        $t5Time = $qT5->fetchColumn();
        if ($t5Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '5', $t5Time, "taskid mulai racik obat BPJS", $stats);

        // TASK ID 6 (Selesai Racik Obat: Data riil resep_obat)
        $qT6 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_perawatan,' ',resep_obat.jam) AS jam FROM resep_obat WHERE resep_obat.tgl_perawatan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :no_rawat LIMIT 1");
        $qT6->execute([':no_rawat' => $noRawat]);
        $t6Time = $qT6->fetchColumn();
        if ($t6Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '6', $t6Time, "taskid selesai racik obat BPJS", $stats);

        // TASK ID 7 (Penyerahan Obat: Data riil resep_obat)
        $qT7 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_penyerahan,' ',resep_obat.jam_penyerahan) AS jam FROM resep_obat WHERE resep_obat.status='ralan' AND resep_obat.no_rawat = :no_rawat AND CONCAT(resep_obat.tgl_penyerahan,' ',resep_obat.jam_penyerahan)<>'0000-00-00 00:00:00' LIMIT 1");
        $qT7->execute([':no_rawat' => $noRawat]);
        $t7Time = $qT7->fetchColumn();
        if ($t7Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '7', $t7Time, "taskid penyerahan obat BPJS", $stats);

        // TASK ID 99 (Pembatalan antrean jika status reg_periksa = Batal)
        $qT99 = $pdo->prepare("SELECT NOW() AS jam FROM reg_periksa WHERE reg_periksa.stts='Batal' AND reg_periksa.no_rawat = :no_rawat LIMIT 1");
        $qT99->execute([':no_rawat' => $noRawat]);
        $t99Time = $qT99->fetchColumn();
        if ($t99Time) sendTaskIdUpdate($pdo, $noBooking, $noRawat, '99', $t99Time, "taskid batal pelayanan BPJS", $stats);
    }
} catch (Exception $e) {
    logMsg("Notifikasi Task ID BPJS Error: " . $e->getMessage());
}

logMsg("Selesai memproses sinkronisasi Antrean Mobile JKN.");

// Respon JSON untuk AJAX Web Dashboard
echo json_encode([
    'status' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'stats' => $stats,
    'logs' => $logs
], JSON_PRETTY_PRINT);
