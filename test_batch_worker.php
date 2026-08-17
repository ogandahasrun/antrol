<?php
/**
 * Backend Batch Worker
 * Memproses 1 Pasien/Kode Booking untuk Fitur Tester Batch
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/bpjs_helper.php';

$action      = isset($_POST['action']) ? trim($_POST['action']) : '';
$kodeBooking = isset($_POST['kodebooking']) ? trim($_POST['kodebooking']) : '';
$noRawat     = isset($_POST['norawat']) && !empty($_POST['norawat']) ? trim($_POST['norawat']) : $kodeBooking;
$customWaktu = isset($_POST['customwaktu']) && !empty($_POST['customwaktu']) ? trim($_POST['customwaktu']) : '';

if (empty($kodeBooking)) {
    echo json_encode([
        'status' => false,
        'message' => 'Kode booking / No. Rawat wajib diisi'
    ]);
    exit;
}

BpjsHelper::initConfig();

/**
 * Helper internal untuk menghitung waktu Task ID berdasarkan Regulasi Utama / Fallback Ide A
 */
function calculateTaskTime($pdo, $rowVal, $noRawatTarget, $taskIdNum, $customWaktu) {
    // 1. Waktu Custom Manual (Override)
    if (!empty($customWaktu)) {
        $waktuCalculated = date('Y-m-d H:i:s', strtotime($customWaktu));
        return [
            'waktu' => $waktuCalculated,
            'info'  => $waktuCalculated . " (Set Waktu Custom Manual Pengguna)"
        ];
    }

    // 2. Waktu Validasi Asli vs Fallback Ide A
    $validasiRaw = $rowVal && !empty($rowVal['validasi']) ? $rowVal['validasi'] : '';
    if (!empty($validasiRaw) && $validasiRaw !== '0000-00-00 00:00:00') {
        $validasiStr = $validasiRaw;
    } else {
        $jamMulai = "08:00:00";
        if ($rowVal && !empty($rowVal['jampraktek'])) {
            $parts = explode('-', $rowVal['jampraktek']);
            $jamMulaiClean = trim($parts[0]);
            if (strlen($jamMulaiClean) >= 5) {
                $jamMulai = substr($jamMulaiClean, 0, 5) . ":00";
            }
        }
        $menit = (int)substr($noRawatTarget, -2);
        $nomorUrut = (int)substr($noRawatTarget, -4);
        $detik = $nomorUrut % 60;
        $tglPeriksa = ($rowVal && !empty($rowVal['tanggalperiksa'])) ? $rowVal['tanggalperiksa'] : date('Y-m-d');
        $baseTime = strtotime("$tglPeriksa $jamMulai");
        $validasiStr = date('Y-m-d H:i:s', $baseTime + ($menit * 60) + $detik);
    }

    $digit14 = strlen($noRawatTarget) >= 14 ? (int)substr($noRawatTarget, 13, 1) : 0;
    $waktuCalculated = $validasiStr;

    if ($taskIdNum === '1') {
        $mod1 = $digit14 % 7;
        $qT1 = $pdo->prepare("SELECT SUBDATE(:val, INTERVAL " . (37 + $mod1) . " MINUTE)");
        $qT1->execute([':val' => $validasiStr]);
        $waktuCalculated = $qT1->fetchColumn();
    } elseif ($taskIdNum === '2') {
        $mod2 = $digit14 % 4;
        $qT2 = $pdo->prepare("SELECT SUBDATE(:val, INTERVAL " . (18 + $mod2) . " MINUTE)");
        $qT2->execute([':val' => $validasiStr]);
        $waktuCalculated = $qT2->fetchColumn();
    } elseif ($taskIdNum === '3') {
        $waktuCalculated = $validasiStr;
    } elseif ($taskIdNum === '4') {
        $mod4 = $digit14 % 3;
        $qT4 = $pdo->prepare("SELECT DATE_ADD(:val, INTERVAL " . (12 + $mod4) . " MINUTE)");
        $qT4->execute([':val' => $validasiStr]);
        $waktuCalculated = $qT4->fetchColumn();
    } elseif ($taskIdNum === '5') {
        $mod5 = $digit14 % 6;
        $qT5 = $pdo->prepare("SELECT DATE_ADD(:val, INTERVAL " . (31 + $mod5) . " MINUTE)");
        $qT5->execute([':val' => $validasiStr]);
        $waktuCalculated = $qT5->fetchColumn();
    } elseif ($taskIdNum === '6') {
        $qT6 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_peresepan,' ',resep_obat.jam_peresepan) AS jam FROM resep_obat WHERE resep_obat.tgl_peresepan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :nr LIMIT 1");
        $qT6->execute([':nr' => $noRawatTarget]);
        $resT6 = $qT6->fetchColumn();
        if ($resT6) $waktuCalculated = $resT6;
    } elseif ($taskIdNum === '7') {
        $qT7 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_perawatan,' ',resep_obat.jam) AS jam FROM resep_obat WHERE resep_obat.tgl_perawatan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :nr LIMIT 1");
        $qT7->execute([':nr' => $noRawatTarget]);
        $resT7 = $qT7->fetchColumn();
        if ($resT7) $waktuCalculated = $resT7;
    } elseif ($taskIdNum === '99') {
        $waktuCalculated = date('Y-m-d H:i:s');
    }

    return [
        'waktu' => $waktuCalculated,
        'info'  => $waktuCalculated . " (Kalkulasi SIMRS/Digit-14: " . $digit14 . ")"
    ];
}

// -----------------------------------------------------------------------------
// 0. AKSI CEK STATUS
// -----------------------------------------------------------------------------
if ($action === 'check') {
    $stmtCheck = $pdo->prepare("SELECT 
            referensi_mobilejkn_bpjs.nobooking,
            referensi_mobilejkn_bpjs.no_rawat,
            referensi_mobilejkn_bpjs.nomorkartu,
            referensi_mobilejkn_bpjs.nik,
            referensi_mobilejkn_bpjs.statuskirim,
            referensi_mobilejkn_bpjs.tanggalperiksa,
            referensi_mobilejkn_bpjs.validasi,
            reg_periksa.no_rkm_medis,
            pasien.nm_pasien,
            poliklinik.nm_poli,
            dokter.nm_dokter
          FROM referensi_mobilejkn_bpjs 
          LEFT JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat=reg_periksa.no_rawat 
          LEFT JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis 
          LEFT JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli 
          LEFT JOIN dokter ON reg_periksa.kd_dokter=dokter.kd_dokter 
          WHERE referensi_mobilejkn_bpjs.nobooking = :kb OR referensi_mobilejkn_bpjs.no_rawat = :nr LIMIT 1");
    $stmtCheck->execute([':kb' => $kodeBooking, ':nr' => $noRawat]);
    $rowCheck = $stmtCheck->fetch();

    $taskLogs = [];
    if ($rowCheck) {
        $stmtTasks = $pdo->prepare("SELECT taskid, waktu FROM referensi_mobilejkn_bpjs_taskid WHERE no_rawat = :nr ORDER BY taskid ASC");
        $stmtTasks->execute([':nr' => $rowCheck['no_rawat']]);
        $taskLogs = $stmtTasks->fetchAll();
    }

    echo json_encode([
        'status' => true,
        'kodebooking' => $kodeBooking,
        'no_rawat' => $rowCheck ? $rowCheck['no_rawat'] : $noRawat,
        'nm_pasien' => $rowCheck ? $rowCheck['nm_pasien'] : 'Tidak Ada di DB Khanza',
        'nm_poli' => $rowCheck ? $rowCheck['nm_poli'] : '-',
        'action' => 'check',
        'found_in_db' => $rowCheck ? true : false,
        'task_logs' => $taskLogs,
        'bpjs_response' => [
            'http_code' => 200,
            'message' => $rowCheck ? 'Ditemukan di SIMRS Khanza' : 'Belum Terdaftar di SIMRS Khanza'
        ]
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// 1. AKSI TAMBAH ANTREAN (/antrean/add)
// -----------------------------------------------------------------------------
if ($action === 'add') {
    $stmt = $pdo->prepare("SELECT 
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
          LEFT JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat=reg_periksa.no_rawat 
          LEFT JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis 
          LEFT JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli 
          LEFT JOIN dokter ON reg_periksa.kd_dokter=dokter.kd_dokter 
          WHERE referensi_mobilejkn_bpjs.nobooking = :kb OR referensi_mobilejkn_bpjs.no_rawat = :nr LIMIT 1");
    $stmt->execute([':kb' => $kodeBooking, ':nr' => $noRawat]);
    $row = $stmt->fetch();

    if ($row) {
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
            "keterangan"      => "Batch request antrean/add"
        ];
    } else {
        $payloadAdd = [
            "kodebooking"     => $kodeBooking,
            "jenispasien"     => "JKN",
            "nomorkartu"      => "0001234567890",
            "nik"             => "3201234567890001",
            "nohp"            => "081234567890",
            "kodepoli"        => "INT",
            "namapoli"        => "POLIKLINIK PENYAKIT DALAM",
            "pasienbaru"      => 0,
            "norm"            => "123456",
            "tanggalperiksa"  => date('Y-m-d'),
            "kodedokter"      => 1234,
            "namadokter"      => "dr. Sp.PD",
            "jampraktek"      => "08:00-12:00",
            "jeniskunjungan"  => 1,
            "nomorreferensi"  => "1234567890123456",
            "nomorantrean"    => "A-1",
            "angkaantrean"    => 1,
            "estimasidilayani"=> time() * 1000,
            "sisakuotajkn"    => 10,
            "kuotajkn"        => 30,
            "sisakuotanonjkn" => 5,
            "kuotanonjkn"     => 15,
            "keterangan"      => "Batch request fallback"
        ];
    }

    $bpjsResult = BpjsHelper::sendRequest("/antrean/add", $payloadAdd, 'POST');

    echo json_encode([
        'status' => true,
        'kodebooking' => $kodeBooking,
        'nm_pasien' => $row ? $row['nm_pasien'] : 'Fallback Pasien',
        'nm_poli' => $row ? $row['nm_poli'] : '-',
        'action' => 'add',
        'request_payload' => $payloadAdd,
        'bpjs_response' => $bpjsResult
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// 2. AKSI TASK ID SPESIFIK (task1 .. task7, task99)
// -----------------------------------------------------------------------------
if (strpos($action, 'task') === 0 && $action !== 'autofull') {
    $taskIdNum = str_replace('task', '', $action);

    $stmtValidasi = $pdo->prepare("SELECT 
            referensi_mobilejkn_bpjs.no_rawat, 
            referensi_mobilejkn_bpjs.validasi, 
            referensi_mobilejkn_bpjs.tanggalperiksa, 
            referensi_mobilejkn_bpjs.jampraktek,
            pasien.nm_pasien,
            poliklinik.nm_poli
          FROM referensi_mobilejkn_bpjs 
          LEFT JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat=reg_periksa.no_rawat
          LEFT JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis
          LEFT JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli
          WHERE referensi_mobilejkn_bpjs.nobooking = :kb OR referensi_mobilejkn_bpjs.no_rawat = :nr LIMIT 1");
    $stmtValidasi->execute([':kb' => $kodeBooking, ':nr' => $noRawat]);
    $rowVal = $stmtValidasi->fetch();

    $noRawatTarget = $rowVal && !empty($rowVal['no_rawat']) ? $rowVal['no_rawat'] : $noRawat;
    $calcResult    = calculateTaskTime($pdo, $rowVal, $noRawatTarget, $taskIdNum, $customWaktu);

    $waktuCalculated = $calcResult['waktu'];
    $epochMs         = strtotime($waktuCalculated) * 1000;

    $payloadTask = [
        "kodebooking" => $kodeBooking,
        "taskid"      => (string)$taskIdNum,
        "waktu"       => (string)$epochMs
    ];

    $bpjsResult = BpjsHelper::sendRequest("/antrean/updatewaktu", $payloadTask, 'POST');

    $resCode = isset($bpjsResult['data']['metadata']['code']) ? (string)$bpjsResult['data']['metadata']['code'] : '';
    $resMsg  = isset($bpjsResult['data']['metadata']['message']) ? $bpjsResult['data']['metadata']['message'] : '';

    if ($resCode === '200' || $resCode === '208' || strpos(strtolower($resMsg), 'sudah') !== false) {
        try {
            $insStmt = $pdo->prepare("INSERT INTO referensi_mobilejkn_bpjs_taskid (no_rawat, taskid, waktu) VALUES (:no_rawat, :taskid, :waktu)");
            $insStmt->execute([':no_rawat' => $noRawatTarget, ':taskid' => (string)$taskIdNum, ':waktu' => $waktuCalculated]);
        } catch (Exception $ex) {}
    }

    echo json_encode([
        'status' => true,
        'kodebooking' => $kodeBooking,
        'nm_pasien' => $rowVal ? $rowVal['nm_pasien'] : 'Pasien',
        'nm_poli' => $rowVal ? $rowVal['nm_poli'] : '-',
        'action' => $action,
        'waktu_calculated' => $waktuCalculated,
        'request_payload' => $payloadTask,
        'bpjs_response' => $bpjsResult
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// 3. AKSI BULK AUTO FULL CYCLE (TASK 1 s.d. 7 SEKALIGUS)
// -----------------------------------------------------------------------------
if ($action === 'autofull') {
    $stmtValidasi = $pdo->prepare("SELECT 
            referensi_mobilejkn_bpjs.no_rawat, 
            referensi_mobilejkn_bpjs.validasi, 
            referensi_mobilejkn_bpjs.tanggalperiksa, 
            referensi_mobilejkn_bpjs.jampraktek,
            pasien.nm_pasien,
            poliklinik.nm_poli
          FROM referensi_mobilejkn_bpjs 
          LEFT JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat=reg_periksa.no_rawat
          LEFT JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis
          LEFT JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli
          WHERE referensi_mobilejkn_bpjs.nobooking = :kb OR referensi_mobilejkn_bpjs.no_rawat = :nr LIMIT 1");
    $stmtValidasi->execute([':kb' => $kodeBooking, ':nr' => $noRawat]);
    $rowVal = $stmtValidasi->fetch();

    $noRawatTarget = $rowVal && !empty($rowVal['no_rawat']) ? $rowVal['no_rawat'] : $noRawat;

    $taskResults = [];
    $allOk = true;

    for ($t = 1; $t <= 7; $t++) {
        $taskIdStr  = (string)$t;
        $calcResult = calculateTaskTime($pdo, $rowVal, $noRawatTarget, $taskIdStr, $customWaktu);
        $waktuCalculated = $calcResult['waktu'];
        $epochMs         = strtotime($waktuCalculated) * 1000;

        $payloadTask = [
            "kodebooking" => $kodeBooking,
            "taskid"      => $taskIdStr,
            "waktu"       => (string)$epochMs
        ];

        $bpjsResult = BpjsHelper::sendRequest("/antrean/updatewaktu", $payloadTask, 'POST');

        $resCode = isset($bpjsResult['data']['metadata']['code']) ? (string)$bpjsResult['data']['metadata']['code'] : '';
        $resMsg  = isset($bpjsResult['data']['metadata']['message']) ? $bpjsResult['data']['metadata']['message'] : '';

        $isSuccess = ($resCode === '200' || $resCode === '208' || strpos(strtolower($resMsg), 'sudah') !== false || strtolower($resMsg) === 'ok');

        if ($isSuccess) {
            try {
                $insStmt = $pdo->prepare("INSERT INTO referensi_mobilejkn_bpjs_taskid (no_rawat, taskid, waktu) VALUES (:no_rawat, :taskid, :waktu)");
                $insStmt->execute([':no_rawat' => $noRawatTarget, ':taskid' => $taskIdStr, ':waktu' => $waktuCalculated]);
            } catch (Exception $ex) {}
        } else {
            $allOk = false;
        }

        $taskResults[] = [
            'taskid' => $taskIdStr,
            'waktu'  => $waktuCalculated,
            'code'   => $resCode,
            'msg'    => $resMsg,
            'bpjs_raw' => $bpjsResult
        ];
    }

    echo json_encode([
        'status' => true,
        'kodebooking' => $kodeBooking,
        'nm_pasien' => $rowVal ? $rowVal['nm_pasien'] : 'Pasien',
        'nm_poli' => $rowVal ? $rowVal['nm_poli'] : '-',
        'action' => 'autofull',
        'overall_success' => $allOk,
        'task_details' => $taskResults
    ]);
    exit;
}

echo json_encode([
    'status' => false,
    'message' => 'Tipe aksi tidak dikenali'
]);
