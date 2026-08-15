<?php
/**
 * Service Worker Engine Bridging Mobile JKN BPJS (PHP Native)
 * Meniru secara 100% logika eksekusi dari Java JAR (frmUtama.java)
 */

// Matikan tampilan error HTML agar tidak merusak format JSON output untuk Dashboard AJAX
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/bpjs_helper.php';

// Nonaktifkan limit waktu eksekusi agar pengiriman ratusan taskid ke BPJS via HTTP tidak timeout
@set_time_limit(0);
@ini_set('max_execution_time', 0);

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

if (!function_exists('logMsg')) {
    function logMsg($msg) {
        global $logs;
        $timeStr = date('H:i:s');
        $logs[] = "[$timeStr] $msg";
    }
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
function isTaskIdProcessed($pdo, $noRawat, $taskId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM referensi_mobilejkn_bpjs_taskid WHERE no_rawat = :no_rawat AND taskid = :taskid");
    $stmt->execute([':no_rawat' => $noRawat, ':taskid' => (string)$taskId]);
    return ((int)$stmt->fetchColumn()) > 0;
}

function sendTaskIdUpdate($pdo, $kodeBooking, $noRawat, $taskId, $waktuStr, $label, &$stats) {
    if (empty($waktuStr) || $waktuStr === '0000-00-00 00:00:00') return false;

    // Cek jika taskid sudah pernah diproses di DB lokal
    if (isTaskIdProcessed($pdo, $noRawat, $taskId)) {
        return true;
    }

    // Simpan temporary record ke referensi_mobilejkn_bpjs_taskid
    try {
        $insStmt = $pdo->prepare("INSERT INTO referensi_mobilejkn_bpjs_taskid (no_rawat, taskid, waktu) VALUES (:no_rawat, :taskid, :waktu)");
        $insStmt->execute([':no_rawat' => $noRawat, ':taskid' => (string)$taskId, ':waktu' => $waktuStr]);
    } catch (Exception $ex) {
        return true;
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

    // Jika BPJS merespon 200 (OK), 208 (Sudah ada/diupdate), atau pesan memuat kata "sudah"/"already"
    $isSuccessOrAlready = ($code === '200' || $code === '208' || strpos(strtolower($msg), 'sudah') !== false || strpos(strtolower($msg), 'already') !== false);

    if ($isSuccessOrAlready) {
        $stats['taskid_success']++;
        return true;
    } else {
        $stats['taskid_failed']++;
        
        // Cek apakah BPJS memberikan respon error bisnis permanen (misal: Task 99 batal, urutan waktu salah, prerequisite belum ada, dll)
        $isPermanentError = (!empty($code) && $code !== '500') || 
                            strpos(strtolower($msg), 'terakhir') !== false ||
                            strpos(strtolower($msg), 'sebelumnya') !== false ||
                            strpos(strtolower($msg), 'belum ada') !== false ||
                            strpos(strtolower($msg), 'tidak valid') !== false ||
                            strpos(strtolower($msg), 'urutan') !== false ||
                            strpos(strtolower($msg), 'melebihi') !== false;

        if ($isPermanentError) {
            logMsg("Task ID $taskId dipertahankan di DB lokal (BPJS Rejection: $code $msg) untuk mencegah infinite retry loop.");
            return false;
        } else {
            // Hapus record dari database jika hanya error koneksi/jaringan biasa agar dapat di-retry pada siklus berikutnya
            $delStmt = $pdo->prepare("DELETE FROM referensi_mobilejkn_bpjs_taskid WHERE taskid = :taskid AND no_rawat = :no_rawat");
            $delStmt->execute([':taskid' => (string)$taskId, ':no_rawat' => $noRawat]);
            return false;
        }
    }
}

// ----------------------------------------------------------------------------------
// 3. PROCESS TASK ID 1 s.d. 7 (PASIEN BPJS)
// ----------------------------------------------------------------------------------
$sqlBpjsTask = "SELECT 
                  referensi_mobilejkn_bpjs.nobooking,
                  referensi_mobilejkn_bpjs.no_rawat,
                  referensi_mobilejkn_bpjs.validasi,
                  referensi_mobilejkn_bpjs.tanggalperiksa,
                  referensi_mobilejkn_bpjs.jampraktek 
                FROM referensi_mobilejkn_bpjs 
                WHERE referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl1 AND :tgl2 
                ORDER BY referensi_mobilejkn_bpjs.tanggalperiksa";

try {
    $stmtBpjs = $pdo->prepare($sqlBpjsTask);
    $stmtBpjs->execute([':tgl1' => $tanggal1, ':tgl2' => $tanggal2]);
    $rowsBpjs = $stmtBpjs->fetchAll();

    $totalPatients = count($rowsBpjs);
    logMsg("Memproses Task ID 1-7 untuk $totalPatients Pasien BPJS...");

    if ($totalPatients === 0) {
        logMsg("Tidak ditemukan antrean pasien BPJS pada periode $tanggal1 s.d. $tanggal2.");
    } else {
        $processedTasksCount = 0;

        foreach ($rowsBpjs as $row) {
        $noRawat = $row['no_rawat'];
        $noBooking = $row['nobooking'];

        // Digit ke-14 no_rawat untuk modulus
        $digit14 = strlen($noRawat) >= 14 ? (int)substr($noRawat, 13, 1) : 0;

        // Penentuan Waktu Validasi Efektif (Gunakan validasi asli DB jika ada; jika kosong/0000-00-00, gunakan Fallback Ide A)
        $validasiEffective = $row['validasi'];
        if (empty($validasiEffective) || $validasiEffective === '0000-00-00 00:00:00') {
            $jamMulai = "08:00:00";
            if (!empty($row['jampraktek'])) {
                $parts = explode('-', $row['jampraktek']);
                $jamMulaiClean = trim($parts[0]);
                if (strlen($jamMulaiClean) >= 5) {
                    $jamMulai = substr($jamMulaiClean, 0, 5) . ":00";
                }
            }
            $menit = (int)substr($noRawat, -2);
            $nomorUrut = (int)substr($noRawat, -4);
            $detik = $nomorUrut % 60;
            $tglPeriksa = !empty($row['tanggalperiksa']) ? $row['tanggalperiksa'] : date('Y-m-d');
            $baseTime = strtotime("$tglPeriksa $jamMulai");
            $validasiEffective = date('Y-m-d H:i:s', $baseTime + ($menit * 60) + $detik);
        }

        // Cek status keberadaan Task 1, 2, dan 3 saat ini
        $task3Done = isTaskIdProcessed($pdo, $noRawat, '3');
        $task1Done = isTaskIdProcessed($pdo, $noRawat, '1');
        $task2Done = isTaskIdProcessed($pdo, $noRawat, '2');

        $patientTasksSent = 0;

        // TASK ID 1 (Mulai Tunggu Poli)
        // ATURAN: Jika Task 3 SUDAH dikirim/tersedia, BPJS menolak Task 1. Maka Task 1 hanya dikirim jika Task 3 BELUM dikirim.
        if (!$task3Done && !$task1Done) {
            $mod1 = $digit14 % 7;
            $qT1 = $pdo->prepare("SELECT SUBDATE(:val, INTERVAL " . (37 + $mod1) . " MINUTE) AS jam");
            $qT1->execute([':val' => $validasiEffective]);
            $t1Time = $qT1->fetchColumn();
            if ($t1Time) {
                $task1Done = sendTaskIdUpdate($pdo, $noBooking, $noRawat, '1', $t1Time, "taskid mulai tunggu poli BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 2 (Mulai Pelayanan Admisi / Loket)
        // ATURAN: Hanya dikirim jika Task 1 SUDAH berhasil/dikirim DAN Task 3 BELUM dikirim.
        if (!$task3Done && $task1Done && !$task2Done) {
            $mod2 = $digit14 % 4;
            $qT2 = $pdo->prepare("SELECT SUBDATE(:val, INTERVAL " . (18 + $mod2) . " MINUTE) AS jam");
            $qT2->execute([':val' => $validasiEffective]);
            $t2Time = $qT2->fetchColumn();
            if ($t2Time) {
                $task2Done = sendTaskIdUpdate($pdo, $noBooking, $noRawat, '2', $t2Time, "taskid mulai pelayanan poli BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 3 (Selesai Pelayanan Admisi / Validasi Poli)
        // ATURAN: Pengecualian! Task 3 BISA dikirim meskipun Task 2 / Task 1 belum tersedia.
        if (!$task3Done) {
            $t3Time = $validasiEffective;
            if ($t3Time) {
                $task3Done = sendTaskIdUpdate($pdo, $noBooking, $noRawat, '3', $t3Time, "taskid selesai pelayanan poli BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 4 (Mulai Pelayanan Dokter Poli)
        // ATURAN: Hanya bisa dikirim jika Task 3 SUDAH dikirim/tersedia.
        if ($task3Done && !isTaskIdProcessed($pdo, $noRawat, '4')) {
            $mod4 = $digit14 % 3;
            $qT4 = $pdo->prepare("SELECT DATE_ADD(:val, INTERVAL " . (12 + $mod4) . " MINUTE) AS jam");
            $qT4->execute([':val' => $validasiEffective]);
            $t4Time = $qT4->fetchColumn();
            if ($t4Time) {
                sendTaskIdUpdate($pdo, $noBooking, $noRawat, '4', $t4Time, "taskid mulai tunggu farmasi BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 5 (Selesai Pelayanan Dokter Poli / Mulai Racik)
        // ATURAN: Hanya bisa dikirim jika Task 4 SUDAH dikirim/tersedia.
        if (isTaskIdProcessed($pdo, $noRawat, '4') && !isTaskIdProcessed($pdo, $noRawat, '5')) {
            $mod5 = $digit14 % 6;
            $qT5 = $pdo->prepare("SELECT DATE_ADD(:val, INTERVAL " . (31 + $mod5) . " MINUTE) AS jam");
            $qT5->execute([':val' => $validasiEffective]);
            $t5Time = $qT5->fetchColumn();
            if ($t5Time) {
                sendTaskIdUpdate($pdo, $noBooking, $noRawat, '5', $t5Time, "taskid mulai racik obat BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 6 (Selesai Racik Obat / Permintaan Resep Poli)
        // ATURAN: Hanya bisa dikirim jika Task 5 SUDAH dikirim/tersedia.
        if (isTaskIdProcessed($pdo, $noRawat, '5') && !isTaskIdProcessed($pdo, $noRawat, '6')) {
            $qT6 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_peresepan,' ',resep_obat.jam_peresepan) AS jam FROM resep_obat WHERE resep_obat.tgl_peresepan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :no_rawat LIMIT 1");
            $qT6->execute([':no_rawat' => $noRawat]);
            $t6Time = $qT6->fetchColumn();
            if ($t6Time) {
                sendTaskIdUpdate($pdo, $noBooking, $noRawat, '6', $t6Time, "taskid selesai racik obat BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 7 (Penyerahan Obat / Selesai Pelayanan Farmasi)
        // ATURAN: Hanya bisa dikirim jika Task 6 SUDAH dikirim/tersedia.
        if (isTaskIdProcessed($pdo, $noRawat, '6') && !isTaskIdProcessed($pdo, $noRawat, '7')) {
            $qT7 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_perawatan,' ',resep_obat.jam) AS jam FROM resep_obat WHERE resep_obat.tgl_perawatan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :no_rawat LIMIT 1");
            $qT7->execute([':no_rawat' => $noRawat]);
            $t7Time = $qT7->fetchColumn();
            if ($t7Time) {
                sendTaskIdUpdate($pdo, $noBooking, $noRawat, '7', $t7Time, "taskid penyerahan obat BPJS", $stats);
                $patientTasksSent++;
            }
        }

        // TASK ID 99 (Pembatalan antrean jika status reg_periksa = Batal)
        if (!isTaskIdProcessed($pdo, $noRawat, '99')) {
            $qT99 = $pdo->prepare("SELECT NOW() AS jam FROM reg_periksa WHERE reg_periksa.stts='Batal' AND reg_periksa.no_rawat = :no_rawat LIMIT 1");
            $qT99->execute([':no_rawat' => $noRawat]);
            $t99Time = $qT99->fetchColumn();
            if ($t99Time) {
                sendTaskIdUpdate($pdo, $noBooking, $noRawat, '99', $t99Time, "taskid batal pelayanan BPJS", $stats);
                $patientTasksSent++;
            }
        }

        $processedTasksCount += $patientTasksSent;
    }

    if ($processedTasksCount === 0) {
        logMsg("Seluruh Task ID (1-7) untuk $totalPatients pasien BPJS periode ini sudah up-to-date / tidak ada task baru yang menggantung.");
    } else {
        logMsg("Selesai memproses $processedTasksCount pembaruan Task ID pasien BPJS.");
    }
}
} catch (Exception $e) {
    logMsg("Notifikasi Task ID BPJS Error: " . $e->getMessage());
}

if (!function_exists('getHariIndo')) {
    function getHariIndo($tglStr) {
        $dayNum = date('N', strtotime($tglStr));
        $days = [
            1 => 'SENIN',
            2 => 'SELASA',
            3 => 'RABU',
            4 => 'KAMIS',
            5 => 'JUMAT',
            6 => 'SABTU',
            7 => 'AKHAD'
        ];
        return isset($days[$dayNum]) ? $days[$dayNum] : '';
    }
}

// ----------------------------------------------------------------------------------
// 4. PROCESS PASIEN ONSITE / NON-BPJS (reg_periksa NOT IN referensi_mobilejkn_bpjs)
// ----------------------------------------------------------------------------------
logMsg("Memproses Pasien Onsite / Non-BPJS (reg_periksa)...");

$sqlOnsite = "SELECT 
                reg_periksa.no_reg,
                reg_periksa.no_rawat,
                reg_periksa.tgl_registrasi,
                reg_periksa.jam_reg,
                reg_periksa.kd_dokter,
                dokter.nm_dokter,
                reg_periksa.kd_poli,
                poliklinik.nm_poli,
                reg_periksa.stts_daftar,
                reg_periksa.no_rkm_medis,
                reg_periksa.kd_pj 
              FROM reg_periksa 
              INNER JOIN dokter ON reg_periksa.kd_dokter=dokter.kd_dokter 
              INNER JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli 
              WHERE reg_periksa.tgl_registrasi BETWEEN :tgl1 AND :tgl2 
                AND reg_periksa.no_rawat NOT IN (
                    SELECT referensi_mobilejkn_bpjs.no_rawat 
                    FROM referensi_mobilejkn_bpjs 
                    WHERE referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl3 AND :tgl4
                ) 
              ORDER BY CONCAT(reg_periksa.tgl_registrasi, ' ', reg_periksa.jam_reg)";

try {
    $stmtOnsite = $pdo->prepare($sqlOnsite);
    $stmtOnsite->execute([
        ':tgl1' => $tanggal1,
        ':tgl2' => $tanggal2,
        ':tgl3' => $tanggal1,
        ':tgl4' => $tanggal2
    ]);
    $rowsOnsite = $stmtOnsite->fetchAll();

    $totalOnsite = count($rowsOnsite);
    logMsg("Memproses $totalOnsite Pasien Onsite/Non-BPJS...");

    $processedOnsiteCount = 0;

    foreach ($rowsOnsite as $row) {
        $noRawat = $row['no_rawat'];
        $tglReg = $row['tgl_registrasi'];
        $jamReg = $row['jam_reg'];
        $hariWork = getHariIndo($tglReg);

        // 1. Cek Jadwal Dokter
        $qJadwal = $pdo->prepare("SELECT * FROM jadwal WHERE hari_kerja = :hari AND kd_dokter = :kd_dokter AND kd_poli = :kd_poli LIMIT 1");
        $qJadwal->execute([':hari' => $hariWork, ':kd_dokter' => $row['kd_dokter'], ':kd_poli' => $row['kd_poli']]);
        $jadwal = $qJadwal->fetch();
        if (!$jadwal) continue;

        // 2. Mapping Kode Dokter & Kode Poli BPJS
        $qMapDokter = $pdo->prepare("SELECT kd_dokter_bpjs FROM maping_dokter_dpjpvclaim WHERE kd_dokter = :kd_dokter LIMIT 1");
        $qMapDokter->execute([':kd_dokter' => $row['kd_dokter']]);
        $kodeDokterBpjs = $qMapDokter->fetchColumn();

        $qMapPoli = $pdo->prepare("SELECT kd_poli_bpjs FROM maping_poli_bpjs WHERE kd_poli_rs = :kd_poli LIMIT 1");
        $qMapPoli->execute([':kd_poli' => $row['kd_poli']]);
        $kodePoliBpjs = $qMapPoli->fetchColumn();

        if (empty($kodeDokterBpjs) || empty($kodePoliBpjs)) continue;

        $jamMulai = $jadwal['jam_mulai'];
        $jamSelesai = $jadwal['jam_selesai'];
        $kuota = (int)$jadwal['kuota'];
        $noRegInt = (int)$row['no_reg'];

        // 3. WS Tambah Antrean Onsite (/antrean/add) jika bukan penjamin BPJS
        if ($row['kd_pj'] !== 'BPJ') {
            $estMulai = date('Y-m-d H:i:s', strtotime("$tglReg $jamMulai") + ($noRegInt * 10 * 60));
            $estMulaiMs = strtotime($estMulai) * 1000;

            $payloadAddOnsite = [
                "kodebooking"     => $noRawat,
                "jenispasien"     => "NON JKN",
                "nomorkartu"      => "-",
                "nik"             => "-",
                "nohp"            => "-",
                "kodepoli"        => $kodePoliBpjs,
                "namapoli"        => $row['nm_poli'],
                "pasienbaru"      => (strpos(strtolower($row['stts_daftar']), 'baru') !== false ? 1 : 0),
                "norm"            => $row['no_rkm_medis'],
                "tanggalperiksa"  => $tglReg,
                "kodedokter"      => (int)$kodeDokterBpjs,
                "namadokter"      => $row['nm_dokter'],
                "jampraktek"      => substr($jamMulai, 0, 5) . "-" . substr($jamSelesai, 0, 5),
                "jeniskunjungan"  => 3,
                "nomorreferensi"  => "-",
                "nomorantrean"    => $row['no_reg'],
                "angkaantrean"    => $noRegInt,
                "estimasidilayani"=> $estMulaiMs,
                "sisakuotajkn"    => max(0, $kuota - $noRegInt),
                "kuotajkn"        => $kuota,
                "sisakuotanonjkn" => max(0, $kuota - $noRegInt),
                "kuotanonjkn"     => $kuota,
                "keterangan"      => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
            ];

            if (!isTaskIdProcessed($pdo, $noRawat, 'add_onsite')) {
                logMsg("JSON Add Onsite: " . json_encode($payloadAddOnsite));
                $resAdd = BpjsHelper::sendRequest("/antrean/add", $payloadAddOnsite, 'POST');
                $codeAdd = isset($resAdd['data']['metadata']['code']) ? (string)$resAdd['data']['metadata']['code'] : '';
                $msgAdd  = isset($resAdd['data']['metadata']['message']) ? $resAdd['data']['metadata']['message'] : '';
                logMsg("Respon WS Add Onsite: $codeAdd $msgAdd");

                if ($codeAdd === '200' || $codeAdd === '208' || strpos(strtolower($msgAdd), 'sudah') !== false) {
                    try {
                        $insAdd = $pdo->prepare("INSERT INTO referensi_mobilejkn_bpjs_taskid (no_rawat, taskid, waktu) VALUES (:nr, 'add_onsite', NOW())");
                        $insAdd->execute([':nr' => $noRawat]);
                    } catch (Exception $eAdd) {}
                }
            }
        }

        // 4. Update Task ID 1..7 & 99 Onsite (/antrean/updatewaktu)
        $digit14 = strlen($noRawat) >= 14 ? (int)substr($noRawat, 13, 1) : 0;
        $onsiteTasksSent = 0;

        $task3Done = isTaskIdProcessed($pdo, $noRawat, '3');
        $task1Done = isTaskIdProcessed($pdo, $noRawat, '1');
        $task2Done = isTaskIdProcessed($pdo, $noRawat, '2');

        // Waktu dasar Onsite
        $dtRegStr = "$tglReg $jamReg";
        $dtMulaiStr = "$tglReg $jamMulai";
        $waktuBaseStr = ($dtRegStr > $dtMulaiStr) ? $dtRegStr : $dtMulaiStr;
        $waktuBaseTs = strtotime($waktuBaseStr);
        $dtRegTs = strtotime($dtRegStr);

        // TASK ID 1 Onsite
        if (!$task3Done && !$task1Done) {
            $mod1 = $digit14 % 4;
            $t1Time = date('Y-m-d H:i:s', $dtRegTs - ((31 + $mod1) * 60));
            $task1Done = sendTaskIdUpdate($pdo, $noRawat, $noRawat, '1', $t1Time, "taskid1 Onsite", $stats);
            $onsiteTasksSent++;
        }

        // TASK ID 2 Onsite
        if (!$task3Done && $task1Done && !$task2Done) {
            $mod2 = $digit14 % 6;
            $t2Time = date('Y-m-d H:i:s', $dtRegTs - ((13 + $mod2) * 60));
            $task2Done = sendTaskIdUpdate($pdo, $noRawat, $noRawat, '2', $t2Time, "taskid2 Onsite", $stats);
            $onsiteTasksSent++;
        }

        // TASK ID 3 Onsite (Waktu Registrasi atau Jam Mulai Dokter, mana yang lebih akhir)
        if (!$task3Done) {
            $t3Time = $waktuBaseStr;
            $task3Done = sendTaskIdUpdate($pdo, $noRawat, $noRawat, '3', $t3Time, "taskid3 Onsite", $stats);
            $onsiteTasksSent++;
        }

        // TASK ID 4 Onsite
        if ($task3Done && !isTaskIdProcessed($pdo, $noRawat, '4')) {
            $mod4 = $digit14 % 8;
            $t4Time = date('Y-m-d H:i:s', $waktuBaseTs + ((7 + $mod4) * 60));
            sendTaskIdUpdate($pdo, $noRawat, $noRawat, '4', $t4Time, "taskid4 Onsite", $stats);
            $onsiteTasksSent++;
        }

        // TASK ID 5 Onsite
        if (isTaskIdProcessed($pdo, $noRawat, '4') && !isTaskIdProcessed($pdo, $noRawat, '5')) {
            $mod5 = $digit14 % 9;
            $t5Time = date('Y-m-d H:i:s', $waktuBaseTs + ((16 + $mod5) * 60));
            sendTaskIdUpdate($pdo, $noRawat, $noRawat, '5', $t5Time, "taskid5 Onsite", $stats);
            $onsiteTasksSent++;
        }

        // TASK ID 6 Onsite
        if (isTaskIdProcessed($pdo, $noRawat, '5') && !isTaskIdProcessed($pdo, $noRawat, '6')) {
            $qT6 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_peresepan,' ',resep_obat.jam_peresepan) AS jam FROM resep_obat WHERE resep_obat.tgl_peresepan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :nr LIMIT 1");
            $qT6->execute([':nr' => $noRawat]);
            $t6Time = $qT6->fetchColumn();
            if ($t6Time) {
                sendTaskIdUpdate($pdo, $noRawat, $noRawat, '6', $t6Time, "taskid6 Onsite", $stats);
                $onsiteTasksSent++;
            }
        }

        // TASK ID 7 Onsite
        if (isTaskIdProcessed($pdo, $noRawat, '6') && !isTaskIdProcessed($pdo, $noRawat, '7')) {
            $qT7 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_perawatan,' ',resep_obat.jam) AS jam FROM resep_obat WHERE resep_obat.tgl_perawatan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :nr LIMIT 1");
            $qT7->execute([':nr' => $noRawat]);
            $t7Time = $qT7->fetchColumn();
            if ($t7Time) {
                sendTaskIdUpdate($pdo, $noRawat, $noRawat, '7', $t7Time, "taskid7 Onsite", $stats);
                $onsiteTasksSent++;
            }
        }

        // TASK ID 99 Onsite
        if (!isTaskIdProcessed($pdo, $noRawat, '99')) {
            $qT99 = $pdo->prepare("SELECT NOW() AS jam FROM reg_periksa WHERE reg_periksa.stts='Batal' AND reg_periksa.no_rawat = :nr LIMIT 1");
            $qT99->execute([':nr' => $noRawat]);
            $t99Time = $qT99->fetchColumn();
            if ($t99Time) {
                sendTaskIdUpdate($pdo, $noRawat, $noRawat, '99', $t99Time, "taskid99 Onsite", $stats);
                $onsiteTasksSent++;
            }
        }

        $processedOnsiteCount += $onsiteTasksSent;
    }

    logMsg("Selesai memproses $processedOnsiteCount pembaruan Task ID pasien Onsite/Non-BPJS.");
} catch (Exception $eOnsite) {
    logMsg("Notifikasi Task ID Onsite Error: " . $eOnsite->getMessage());
}

logMsg("Selesai memproses sinkronisasi Antrean Mobile JKN.");

// Respon JSON untuk AJAX Web Dashboard
if (ob_get_length()) ob_end_clean();
echo json_encode([
    'status' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'stats' => $stats,
    'logs' => $logs
], JSON_PRETTY_PRINT);
