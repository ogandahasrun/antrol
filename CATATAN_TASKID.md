# Catatan Teknis Kalkulasi & Penanganan Task ID SIMRS Mobile JKN / Antrol

Dokumen ini berisi catatan teknis mengenai logika kalkulasi waktu Task ID 1 s.d. 7 serta perilaku sistem ketika waktu `validasi` tidak tersedia pada modul sinkronisasi Antrean Online BPJS (Antrol SIMRS Khanza).

---

## 1. Logika Fallback Waktu Validasi (Ide A)

Jika kolom `referensi_mobilejkn_bpjs.validasi` bernilai `'0000-00-00 00:00:00'` atau `NULL` (misal pasien belum sempat di-validasi di SIMRS), sistem secara otomatis mengaktifkan **Fallback Waktu Validasi Efektif (Ide A)** dengan komponen berikut:

1. **Jam Mulai Praktek (`jamMulai`)**:
   Diambil dari awal rentang jam praktek dokter (`referensi_mobilejkn_bpjs.jampraktek`), contoh `"08:00 - 14:00"` $\rightarrow$ `"08:00:00"`.
2. **Menit Dari No. Rawat (`menit`)**:
   Diambil dari 2 digit terakhir `no_rawat` (contoh: `2026/08/15/0000044` $\rightarrow$ 44 menit, `0000195` $\rightarrow$ 95 menit).
3. **Detik Alami Modulus 60 (`detik`)**:
   Diambil dari sisa hasil bagi 60 dari 4 digit terakhir `no_rawat` (`(int)substr($noRawat, -4) % 60`), sehingga menghasilkan detik 00–59 secara alami dan unik per pasien.
4. **Formula Penggabungan**:
   ```php
   $validasiEffective = date('Y-m-d H:i:s', strtotime("$tglPeriksa $jamMulai + $menit minutes + $detik seconds"));
   ```
   *Contoh Hasil:*
   - `no_rawat` ending `...000044` dengan praktek `08:00` $\rightarrow$ `08:44:44`.
   - `no_rawat` ending `...000195` dengan praktek `08:00` $\rightarrow$ 95 menit (1j 35m) + 15 detik $\rightarrow$ `09:35:15`.

> [!IMPORTANT]
> **Jaminan Keamanan Fitur Existing**: Jika `validasi` di database **SUDAH TERISI / VALID**, sistem **100% tetap menggunakan nilai `validasi` asli tersebut** tanpa perubahan apa pun.

---

## 2. Kalkulasi Waktu Task ID (1 s.d. 7)

Waktu `validasiEffective` (baik dari DB asli maupun hasil kalkulasi Fallback Ide A) digunakan sebagai **titik acuan utama (*anchor time*)**.

### 🎲 Variasi Modulus Digit Ke-14 (`no_rawat`)
Untuk menghindari stempel waktu (*timestamp*) yang seragam antar-pasien, sistem mengambil digit ke-14 dari string `no_rawat`:
```php
$digit14 = strlen($noRawat) >= 14 ? (int)substr($noRawat, 13, 1) : 0;
```

---

### ⏱️ Rincian Formula Tiap Task ID

| Task ID | Nama Task / Keterangan | Metode / Acuan Waktu | Formula & Variasi Offset |
| :--- | :--- | :--- | :--- |
| **Task 3** | Selesai Pelayanan Admisi / Validasi Poli | **Baseline (Anchor)** | `$validasiEffective` |
| **Task 1** | Mulai Tunggu Poli (Check-in) | Hitung Mundur | `SUBDATE(:val, INTERVAL (37 + (digit14 % 7)) MINUTE)`<br>*(~37 s.d. 43 menit sebelum validasi)* |
| **Task 2** | Mulai Pelayanan Admisi / Loket | Hitung Mundur | `SUBDATE(:val, INTERVAL (18 + (digit14 % 4)) MINUTE)`<br>*(~18 s.d. 21 menit sebelum validasi)* |
| **Task 4** | Mulai Pelayanan Dokter Poli | Hitung Maju | `DATE_ADD(:val, INTERVAL (12 + (digit14 % 3)) MINUTE)`<br>*(~12 s.d. 14 menit setelah validasi)* |
| **Task 5** | Selesai Dokter Poli / Mulai Racik Obat | Hitung Maju | `DATE_ADD(:val, INTERVAL (31 + (digit14 % 6)) MINUTE)`<br>*(~31 s.d. 36 menit setelah validasi)* |
| **Task 6** | Selesai Racik Obat / Permintaan Resep | Real SIMRS (`resep_obat`) | `CONCAT(resep_obat.tgl_peresepan, ' ', resep_obat.jam_peresepan)` |
| **Task 7** | Penyerahan Obat / Selesai Farmasi | Real SIMRS (`resep_obat`) | `CONCAT(resep_obat.tgl_perawatan, ' ', resep_obat.jam)` |

---

## 3. Diagram Skema Alur Waktu (*Timeline*)

```text
T1 (Check-in Poli)   ---> -40 Menit dari validasiEffective
T2 (Mulai Loket)     ---> -20 Menit dari validasiEffective
T3 (Validasi Admisi) ---> 0 Menit (validasiEffective)
T4 (Mulai Dokter)    ---> +13 Menit dari validasiEffective
T5 (Selesai Dokter)  ---> +33 Menit dari validasiEffective
T6 (Selesai Resep)   ---> Input Real (resep_obat.jam_peresepan)
T7 (Penyerahan Obat) ---> Input Real (resep_obat.jam)
```

---

## 4. Referensi Source Code Terkait

* `service_antrol.php` — Script Worker Engine Sync utama.
* `test_single_worker.php` — Worker pengujian per single pasien / manual trigger.
* `engine_sync.php` — UI Dasbor Monitor Engine Sync.
