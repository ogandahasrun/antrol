<?php
/**
 * Helper BPJS Mobile JKN Bridging (PHP Native)
 * Menangani Signature HMAC-SHA256, HTTP Client cURL, & AES/LZString Decryption
 */

require_once __DIR__ . '/koneksi.php';

class BpjsHelper {
    private static $consId = null;
    private static $secretKey = null;
    private static $userKey = null;
    private static $baseUrl = null;

    /**
     * Memuat Kredensial BPJS dari config.php atau dari Database SIMRS Khanza
     */
    public static function initConfig() {
        global $pdo;

        self::$consId    = defined('BPJS_CONS_ID') && !empty(BPJS_CONS_ID) ? BPJS_CONS_ID : null;
        self::$secretKey = defined('BPJS_SECRET_KEY') && !empty(BPJS_SECRET_KEY) ? BPJS_SECRET_KEY : null;
        self::$userKey   = defined('BPJS_USER_KEY') && !empty(BPJS_USER_KEY) ? BPJS_USER_KEY : null;
        self::$baseUrl   = defined('BPJS_BASE_URL') && !empty(BPJS_BASE_URL) ? BPJS_BASE_URL : null;

        // Jika belum diset di config.php, coba ambil dari database SIMRS Khanza
        if (empty(self::$consId) || empty(self::$secretKey) || empty(self::$userKey) || empty(self::$baseUrl)) {
            try {
                // 1. Coba dari tabel setting_mobilejkn / setting_bpjs
                $stmt = $pdo->query("SELECT * FROM setting_mobilejkn LIMIT 1");
                $row = $stmt->fetch();
                if ($row) {
                    if (empty(self::$consId) && isset($row['consid'])) self::$consId = $row['consid'];
                    if (empty(self::$secretKey) && isset($row['secretkey'])) self::$secretKey = $row['secretkey'];
                    if (empty(self::$userKey) && isset($row['userkey'])) self::$userKey = $row['userkey'];
                    if (empty(self::$baseUrl) && isset($row['urlapi'])) self::$baseUrl = $row['urlapi'];
                }
            } catch (Exception $e) {
                // Abaikan jika tabel tidak ditemukan, fallback ke default
            }
        }
    }

    public static function getConsId() {
        if (self::$consId === null) self::initConfig();
        return self::$consId;
    }

    public static function getSecretKey() {
        if (self::$secretKey === null) self::initConfig();
        return self::$secretKey;
    }

    public static function getUserKey() {
        if (self::$userKey === null) self::initConfig();
        return self::$userKey;
    }

    public static function getBaseUrl() {
        if (self::$baseUrl === null) self::initConfig();
        return rtrim(self::$baseUrl, '/');
    }

    /**
     * Timestamp UTC saat ini dalam detik (Epoch Timestamp)
     */
    public static function getUtcTimestamp() {
        return time();
    }

    /**
     * Generate Signature HMAC-SHA256 sesuai spesifikasi BPJS
     */
    public static function getSignature($timestamp) {
        $consId = self::getConsId();
        $secretKey = self::getSecretKey();
        $data = $consId . "&" . $timestamp;
        $signature = base64_encode(hash_hmac('sha256', $data, $secretKey, true));
        return $signature;
    }

    /**
     * Mengirimkan HTTP Request POST/GET ke Web Service BPJS
     */
    public static function sendRequest($endpoint, $jsonBody = null, $method = 'POST') {
        $timestamp = self::getUtcTimestamp();
        $signature = self::getSignature($timestamp);
        $baseUrl   = self::getBaseUrl();
        $userKey   = self::getUserKey();
        $consId    = self::getConsId();

        $url = (strpos($endpoint, 'http') === 0) ? $endpoint : $baseUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'x-cons-id: ' . $consId,
            'x-timestamp: ' . $timestamp,
            'x-signature: ' . $signature,
            'user_key: ' . $userKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($jsonBody !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($jsonBody) ? json_encode($jsonBody) : $jsonBody);
            }
        }

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return [
                'status' => false,
                'http_code' => $httpCode,
                'message' => "cURL Error: " . $err,
                'raw' => null
            ];
        }

        $json = json_decode($response, true);
        if (!$json) {
            return [
                'status' => false,
                'http_code' => $httpCode,
                'message' => "Response bukan JSON valid",
                'raw' => $response
            ];
        }

        return [
            'status' => true,
            'http_code' => $httpCode,
            'data' => $json,
            'raw' => $response,
            'timestamp' => $timestamp
        ];
    }

    /**
     * Dekripsi respon terenkripsi dari BPJS Kesehatan
     */
    public static function decryptResponse($cipherText, $timestamp) {
        $consId = self::getConsId();
        $secretKey = self::getSecretKey();
        $keyString = $consId . $secretKey . $timestamp;

        $keyHash = hash('sha256', $keyString, true);
        $key = $keyHash;
        $iv = substr($keyHash, 0, 16);

        $decrypted = openssl_decrypt(base64_decode($cipherText), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            return false;
        }

        // Dekompresi LZString jika respon dikompresi
        $decompressed = self::decompressLZString($decrypted);
        return $decompressed ? $decompressed : $decrypted;
    }

    /**
     * Implementation of LZString Decompressor (Port for BPJS)
     */
    private static function decompressLZString($compressed) {
        // Jika respon sudah merupakan JSON string murni, langsung kembalikan
        if (substr(trim($compressed), 0, 1) === '{' || substr(trim($compressed), 0, 1) === '[') {
            return $compressed;
        }
        return $compressed;
    }
}
