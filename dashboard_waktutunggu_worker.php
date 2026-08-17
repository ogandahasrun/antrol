<?php
/**
 * Backend Worker untuk Dashboard Waktu Tunggu BPJS
 * Endpoint: /dashboard/waktutunggu/tanggal/{tanggal}/waktu/{waktu}
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/bpjs_helper.php';

BpjsHelper::initConfig();

$tanggal = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : date('Y-m-d');
$waktu   = isset($_POST['waktu']) && in_array(strtolower($_POST['waktu']), ['rs', 'server']) ? strtolower($_POST['waktu']) : 'rs';

// Validasi format tanggal YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    echo json_encode([
        'status' => false,
        'message' => 'Format tanggal tidak valid (Gunakan YYYY-MM-DD)'
    ]);
    exit;
}

$endpoint = "/dashboard/waktutunggu/tanggal/{$tanggal}/waktu/{$waktu}";

$res = BpjsHelper::sendRequest($endpoint, null, 'GET');

if (!$res['status']) {
    echo json_encode([
        'status' => false,
        'endpoint' => $endpoint,
        'message' => $res['message'] ?? 'Gagal melakukan koneksi ke Web Service BPJS',
        'raw_response' => $res
    ]);
    exit;
}

$responseData = $res['data'] ?? null;
$decryptedData = null;

if ($responseData && isset($responseData['response']) && is_string($responseData['response']) && !empty($res['timestamp'])) {
    $decryptedStr = BpjsHelper::decryptResponse($responseData['response'], $res['timestamp']);
    if ($decryptedStr) {
        $decryptedData = json_decode($decryptedStr, true);
    }
} elseif ($responseData && isset($responseData['response']) && (is_array($responseData['response']) || is_object($responseData['response']))) {
    $decryptedData = $responseData['response'];
}

echo json_encode([
    'status' => true,
    'endpoint' => $endpoint,
    'tanggal' => $tanggal,
    'waktu_tipe' => $waktu,
    'bpjs_raw' => $responseData,
    'decrypted' => $decryptedData ?? ($responseData['response'] ?? null),
    'metadata' => $responseData['metadata'] ?? null
]);
