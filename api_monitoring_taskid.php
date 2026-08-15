<?php
/**
 * API Backend Data Monitoring & Kontrol Task ID Mobile JKN
 * Mengembalikan data JSON 14 kolom lengkap untuk antrean BPJS & Onsite
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/koneksi.php';

$tanggal1 = isset($_GET['tanggal1']) && !empty($_GET['tanggal1']) ? $_GET['tanggal1'] : date('Y-m-d');
$tanggal2 = isset($_GET['tanggal2']) && !empty($_GET['tanggal2']) ? $_GET['tanggal2'] : date('Y-m-d');
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'semua'; // semua, lengkap, belum

// 1. Ambil Data Pasien Mobile JKN BPJS
$sqlBpjs = "SELECT 
              referensi_mobilejkn_bpjs.nobooking,
              referensi_mobilejkn_bpjs.no_rawat,
              referensi_mobilejkn_bpjs.tanggalperiksa,
              reg_periksa.no_rkm_medis,
              pasien.nm_pasien,
              poliklinik.nm_poli,
              dokter.nm_dokter
            FROM referensi_mobilejkn_bpjs
            INNER JOIN reg_periksa ON referensi_mobilejkn_bpjs.no_rawat = reg_periksa.no_rawat
            INNER JOIN pasien ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
            INNER JOIN poliklinik ON reg_periksa.kd_poli = poliklinik.kd_poli
            INNER JOIN dokter ON reg_periksa.kd_dokter = dokter.kd_dokter
            WHERE referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl1 AND :tgl2
            ORDER BY referensi_mobilejkn_bpjs.tanggalperiksa, referensi_mobilejkn_bpjs.nobooking";

$stmtBpjs = $pdo->prepare($sqlBpjs);
$stmtBpjs->execute([':tgl1' => $tanggal1, ':tgl2' => $tanggal2]);
$rowsBpjs = $stmtBpjs->fetchAll(PDO::FETCH_ASSOC);

// 2. Ambil Data Pasien Onsite / Non-BPJS
$sqlOnsite = "SELECT 
                reg_periksa.no_rawat AS nobooking,
                reg_periksa.no_rawat,
                reg_periksa.tgl_registrasi AS tanggalperiksa,
                reg_periksa.no_rkm_medis,
                pasien.nm_pasien,
                poliklinik.nm_poli,
                dokter.nm_dokter
              FROM reg_periksa
              INNER JOIN pasien ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
              INNER JOIN poliklinik ON reg_periksa.kd_poli = poliklinik.kd_poli
              INNER JOIN dokter ON reg_periksa.kd_dokter = dokter.kd_dokter
              WHERE reg_periksa.tgl_registrasi BETWEEN :tgl1 AND :tgl2
                AND reg_periksa.no_rawat NOT IN (
                    SELECT referensi_mobilejkn_bpjs.no_rawat 
                    FROM referensi_mobilejkn_bpjs 
                    WHERE referensi_mobilejkn_bpjs.tanggalperiksa BETWEEN :tgl3 AND :tgl4
                )
              ORDER BY reg_periksa.tgl_registrasi, reg_periksa.jam_reg";

$stmtOnsite = $pdo->prepare($sqlOnsite);
$stmtOnsite->execute([
    ':tgl1' => $tanggal1,
    ':tgl2' => $tanggal2,
    ':tgl3' => $tanggal1,
    ':tgl4' => $tanggal2
]);
$rowsOnsite = $stmtOnsite->fetchAll(PDO::FETCH_ASSOC);

// Gabungkan kedua sumber data pasien
$allPatients = array_merge($rowsBpjs, $rowsOnsite);

$listData = [];
$statsTotal = count($allPatients);
$statsLengkap = 0;
$statsBelumLengkap = 0;
$statsBerResep = 0;
$statsTanpaResep = 0;

foreach ($allPatients as $p) {
    $noRawat = $p['no_rawat'];
    $noBooking = $p['nobooking'];

    // Pull Task ID dari DB referensi_mobilejkn_bpjs_taskid
    $qTasks = $pdo->prepare("SELECT taskid, waktu FROM referensi_mobilejkn_bpjs_taskid WHERE no_rawat = :nr");
    $qTasks->execute([':nr' => $noRawat]);
    $dbTasks = $qTasks->fetchAll(PDO::FETCH_KEY_PAIR); // ['1' => '2026-08-13 09:00:00', ...]

    // Cek keberadaan resep obat ralan
    $qResep = $pdo->prepare("SELECT COUNT(*) FROM resep_obat WHERE no_rawat = :nr AND status = 'ralan'");
    $qResep->execute([':nr' => $noRawat]);
    $hasResep = ((int)$qResep->fetchColumn()) > 0;

    if ($hasResep) {
        $statsBerResep++;
    } else {
        $statsTanpaResep++;
    }

    // Evaluasi Task 1-7
    $t1 = isset($dbTasks['1']) ? $dbTasks['1'] : '-';
    $t2 = isset($dbTasks['2']) ? $dbTasks['2'] : '-';
    $t3 = isset($dbTasks['3']) ? $dbTasks['3'] : '-';
    $t4 = isset($dbTasks['4']) ? $dbTasks['4'] : '-';
    $t5 = isset($dbTasks['5']) ? $dbTasks['5'] : '-';
    
    if ($hasResep) {
        $t6 = isset($dbTasks['6']) ? $dbTasks['6'] : '-';
        $t7 = isset($dbTasks['7']) ? $dbTasks['7'] : '-';
    } else {
        $t6 = isset($dbTasks['6']) ? $dbTasks['6'] : 'N/A (Tanpa Resep)';
        $t7 = isset($dbTasks['7']) ? $dbTasks['7'] : 'N/A (Tanpa Resep)';
    }

    // Aturan Kelengkapan Status Kirim:
    // Task 1 & 2: Opsional (tidak wajib)
    // Task 3, 4, 5: Wajib
    // Task 6 & 7: Wajib jika pasien ber-resep
    $task3Ok = isset($dbTasks['3']);
    $task4Ok = isset($dbTasks['4']);
    $task5Ok = isset($dbTasks['5']);
    $task6Ok = !$hasResep || isset($dbTasks['6']);
    $task7Ok = !$hasResep || isset($dbTasks['7']);

    $isLengkap = ($task3Ok && $task4Ok && $task5Ok && $task6Ok && $task7Ok);

    if ($isLengkap) {
        $statsLengkap++;
        $statusKirimLabel = 'Sudah Lengkap';
        $statusKirimCode = 'lengkap';
    } else {
        $statsBelumLengkap++;
        $statusKirimLabel = 'Belum Lengkap';
        $statusKirimCode = 'belum';
    }

    // Apply Filter Status
    if ($filterStatus === 'lengkap' && !$isLengkap) continue;
    if ($filterStatus === 'belum' && $isLengkap) continue;

    $listData[] = [
        'nobooking'       => $noBooking,
        'no_rawat'        => $noRawat,
        'no_rkm_medis'    => $p['no_rkm_medis'],
        'nm_pasien'       => $p['nm_pasien'],
        'nm_poli'         => $p['nm_poli'],
        'nm_dokter'       => $p['nm_dokter'],
        'tanggalperiksa'  => $p['tanggalperiksa'],
        'has_resep'       => $hasResep,
        'task1'           => $t1,
        'task2'           => $t2,
        'task3'           => $t3,
        'task4'           => $t4,
        'task5'           => $t5,
        'task6'           => $t6,
        'task7'           => $t7,
        'is_lengkap'      => $isLengkap,
        'status_kirim'    => $statusKirimLabel,
        'status_code'     => $statusKirimCode
    ];
}

if (ob_get_length()) ob_end_clean();

echo json_encode([
    'status' => true,
    'tanggal1' => $tanggal1,
    'tanggal2' => $tanggal2,
    'summary' => [
        'total_pasien'   => $statsTotal,
        'sudah_lengkap'  => $statsLengkap,
        'belum_lengkap'  => $statsBelumLengkap,
        'ber_resep'      => $statsBerResep,
        'tanpa_resep'    => $statsTanpaResep
    ],
    'data' => $listData
], JSON_PRETTY_PRINT);
