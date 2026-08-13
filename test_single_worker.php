<?php
/**
 * Backend Single Booking Tester Worker
 * Memproses Uji Coba Pengiriman 1 Kode Booking / Task ID ke Web Service BPJS
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/bpjs_helper.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$kodeBooking = isset($_POST['kodebooking']) ? trim($_POST['kodebooking']) : '';
$noRawat = isset($_POST['norawat']) && !empty($_POST['norawat']) ? trim($_POST['norawat']) : $kodeBooking;

if (empty($kodeBooking)) {
    echo json_encode([
        'status' => false,
        'message' => 'Kode booking / No. Rawat wajib diisi'
    ]);
    exit;
}

BpjsHelper::initConfig();

$responsePayload = [];
$bpjsResult = null;

// 1. TAMBAH ANTREAN (/antrean/add)
if ($action === 'add') {
    // Coba ambil data riil dari database jika ada
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
            "keterangan"      => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
        ];
    } else {
        // Fallback Sample Payload jika data tidak ditemukan di DB
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
            "keterangan"      => "Uji Coba Single Request"
        ];
    }

    $responsePayload = $payloadAdd;
    $bpjsResult = BpjsHelper::sendRequest("/antrean/add", $payloadAdd, 'POST');

} 
// 2. UPDATE TASK ID 1 s.d. 7 & 99 (/antrean/updatewaktu)
elseif (strpos($action, 'task') === 0) {
    $taskIdNum = str_replace('task', '', $action);

    // Ambil no_rawat dan validasi dari DB jika ada
    $stmtValidasi = $pdo->prepare("SELECT no_rawat, validasi FROM referensi_mobilejkn_bpjs WHERE nobooking = :kb OR no_rawat = :nr LIMIT 1");
    $stmtValidasi->execute([':kb' => $kodeBooking, ':nr' => $noRawat]);
    $rowVal = $stmtValidasi->fetch();

    $noRawatTarget = $rowVal && !empty($rowVal['no_rawat']) ? $rowVal['no_rawat'] : $noRawat;
    $validasiStr   = $rowVal && !empty($rowVal['validasi']) ? $rowVal['validasi'] : date('Y-m-d H:i:s');
    $digit14       = strlen($noRawatTarget) >= 14 ? (int)substr($noRawatTarget, 13, 1) : 0;

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
        $qT6 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_perawatan,' ',resep_obat.jam) AS jam FROM resep_obat WHERE resep_obat.tgl_perawatan<>'0000-00-00' AND resep_obat.status='ralan' AND resep_obat.no_rawat = :nr LIMIT 1");
        $qT6->execute([':nr' => $noRawatTarget]);
        $resT6 = $qT6->fetchColumn();
        if ($resT6) $waktuCalculated = $resT6;
    } elseif ($taskIdNum === '7') {
        $qT7 = $pdo->prepare("SELECT CONCAT(resep_obat.tgl_penyerahan,' ',resep_obat.jam_penyerahan) AS jam FROM resep_obat WHERE resep_obat.status='ralan' AND resep_obat.no_rawat = :nr AND CONCAT(resep_obat.tgl_penyerahan,' ',resep_obat.jam_penyerahan)<>'0000-00-00 00:00:00' LIMIT 1");
        $qT7->execute([':nr' => $noRawatTarget]);
        $resT7 = $qT7->fetchColumn();
        if ($resT7) $waktuCalculated = $resT7;
    } elseif ($taskIdNum === '99') {
        $waktuCalculated = date('Y-m-d H:i:s');
    }

    $epochMs = strtotime($waktuCalculated) * 1000;

    $payloadTask = [
        "kodebooking" => $kodeBooking,
        "taskid"      => (string)$taskIdNum,
        "waktu"       => (string)$epochMs,
        "_info_waktu_readable" => $waktuCalculated . " (Kalkulasi Modulus Digit-14: " . $digit14 . ")"
    ];

    $responsePayload = $payloadTask;
    $bpjsResult = BpjsHelper::sendRequest("/antrean/updatewaktu", [
        "kodebooking" => $kodeBooking,
        "taskid"      => (string)$taskIdNum,
        "waktu"       => (string)$epochMs
    ], 'POST');
} 
// 3. BATAL ANTREAN (/antrean/batal)
elseif ($action === 'batal') {
    $payloadBatal = [
        "kodebooking" => $kodeBooking,
        "keterangan"  => "Pembatalan Uji Coba Single Request"
    ];
    $responsePayload = $payloadBatal;
    $bpjsResult = BpjsHelper::sendRequest("/antrean/batal", $payloadBatal, 'POST');
}

echo json_encode([
    'status' => true,
    'endpoint_url' => BpjsHelper::getBaseUrl(),
    'request_payload' => $responsePayload,
    'bpjs_response' => $bpjsResult
], JSON_PRETTY_PRINT);
