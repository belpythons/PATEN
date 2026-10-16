# Business Requirements Document (BRD)
**Nama Proyek:** PATEN (Pemantauan Transaksi & Efektivitas Niaga)  
**Wilayah Fokus:** Kabupaten Kutai Timur (Kutim), Kalimantan Timur  
**Target Instansi & Ekosistem:** Dinas Koperasi & UKM (Diskop-UKM) Kutim, Disporapar, Bapenda, CSR Korporasi (PT KPC, dll.), BAZNAS Kutim, Perbankan Daerah (Bankaltimtara & BPR Kutim), EO Lokal, dan Pelaku UMKM.

---

## 1. Latar Belakang Bisnis & Masalah Lapangan

Kabupaten Kutai Timur tengah mendorong transformasi ekonomi daerah dari ketergantungan sektor ekstraktif (tambang batubara dan kelapa sawit) menuju kemandirian sektor riil melalui pemberdayaan sekitar $20.000$ pelaku UMKM lokal. Salah satu instrumen utama pemerintah daerah dan korporasi (CSR) adalah penyelenggaraan event keramaian seperti **Bazar Gebyar UMKM 18 Kecamatan** dan **Pekan Raya Kutim Expo**.

Namun, terdapat **empat masalah fundamental** di lapangan saat ini:
1. **Klaim Perputaran Uang Tanpa Bukti Riil:** Laporan pertanggungjawaban (SPJ) penyelenggaraan event selama ini hanya berisi dokumentasi foto dan taksiran kasar/lisan mengenai perputaran uang (omzet), sehingga sulit dipertanggungjawabkan secara objektif kepada Bupati, DPRD, Inspektorat/BPK, maupun dewan direksi CSR sponsor.
2. **Ketimpangan Kuota Hibah vs Populasi UMKM:** Target penyaluran Hibah Modal APBD Diskop-UKM Kutim berkisar pada $200$ pelaku usaha per tahun dari total populasi sekitar $20.000$ UMKM (proporsi $\approx 1\%$). Tanpa data transaksi riil, proses seleksi rentan subjektif, rawan tudingan pilih kasih, atau jatuh ke "UMKM musiman/fiktif".
3. **Pembinaan Berulang pada Orang yang Sama:** Dari sekitar $12.000$ UMKM yang mulai terdata di basis data daerah, belum ada instrumen ukur performa otomatis untuk memisahkan UMKM yang butuh *Fasilitasi Legalitas*, *Hibah Alat/Modal*, atau *Kredit Lunak Perbankan*.
4. **Resistensi Pelaku UMKM Terhadap Sistem Rumit & Isu Pajak:** Pedagang di lapangan enggan menggunakan aplikasi kasir (POS) yang rumit saat antrean ramai, serta menaruh curiga apabila pelaporan omzet diartikan sebagai dasar penarikan pajak atau kenaikan harga sewa stand.

---

## 2. Tujuan Bisnis (Business Objectives)

1. **Akuntabilitas Event 100% Terukur:** Mengubah klaim perputaran uang berbasis taksiran menjadi data *Gross Merchandise Value* (GMV) dan volume kehadiran warga yang tervalidasi secara digital.
2. **Kurasi Bantuan Berbasis Merit & Regulasi (Evidence-Based Policy):** Menyediakan Sistem Pendukung Keputusan (SPK) terintegrasi Machine Learning yang menyaring kandidat penerima program bantuan UMKM Kutai Timur secara objektif dan patuh terhadap Peraturan Bupati (Perbup).
3. **Adopsi Tanpa Hambatan (Zero-Friction Adoption):** Menghadirkan mekanisme pencatatan transaksi di stand UMKM yang selesai dalam waktu $\le 3$ detik per transaksi, dibingkai sebagai "Portofolio Syarat Bantuan Usaha" agar pedagang antusias mencatat penjualannya.
4. **Efisiensi Promosi & Operasional Penyelenggara:** Memangkas waktu pembuatan landing page promosi, poster publikasi ber-QR, pengaturan jadwal, serta pengelolaan daftar harga menu stand ke dalam satu alur otomatis.

---

## 3. Pemangku Kepentingan (Stakeholders) & Nilai Tambah

| Stakeholder | Peran | Masalah Saat Ini | Solusi & Nilai Tambah dari PATEN |
| :--- | :--- | :--- | :--- |
| **Diskop-UKM Kutim** | Pemilik Program Bantuan & Pembina UMKM | Sulit menyaring $200$ penerima hibah secara objektif dari $20.000$ UMKM; risiko bantuan berulang pada orang yang sama. | Mendapatkan hasil pemeringkatan SPK + ML otomatis yang memetakan UMKM ke dalam 5 program bantuan daerah sesuai syarat Perbup. |
| **Sponsor CSR (PT KPC, dll.) & BAZNAS** | Penyedia Dana CSR & Bantuan Produktif | Laporan dampak event dari EO hanya berupa klaim keramaian tanpa rasio dampak ekonomi. | Menerima metrik riil rasio dana sponsor terhadap perputaran uang lokal dan jumlah transaksi UMKM terbina. |
| **Event Organizer (EO) / Panitia Kecamatan** | Pelaksana Teknis Event | Repot membuat web info acara, mendesain poster, merekap kehadiran pengunjung, dan menagih laporan omzet manual ke tenda-tenda. | Mendapatkan *Microsite Event Generator*, *Poster Generator*, sistem kupon doorprize/HTM otomatis, dan *CMS Hidden URL* praktis. |
| **Pelaku UMKM Lokal (Tenant Stand)** | Pengguna POS Mini & Sasaran Bantuan | Sibuk melayani pembeli, tidak punya buku kas rapi, takut dipajaki, dan sulit mengakses kuota hibah pemerintah. | Mendapatkan POS super simpel (cukup pilih dropdown menu), cetak desain menu gratis, dan masuk antrean prioritas kurasi bantuan resmi. |
| **Masyarakat / Pengunjung** | Konsumen Event | Kesulitan mendapat informasi jadwal acara terkini dan mekanisme kupon doorprize masih manual (kertas). | Akses mudah ke halaman info event via scan QR poster dan pendaftaran undian doorprize/tiket digital langsung dari HP. |

---

## 4. Kepatuhan Regulasi & Program Bantuan UMKM Kutai Timur

Sistem PATEN dirancang selaras dengan aturan main dan skema intervensi yang berjalan di Kabupaten Kutai Timur:

### A. Aturan Kepatuhan Mutlak (Berdasarkan Perbup Hibah UMKM Kutim)
1. **Aturan 1 KK = 1 Penerima:** Hanya diperbolehkan maksimal 1 penerima bantuan dalam 1 Kartu Keluarga (`no_kk`).
2. **Syarat Legalitas & Domisili:** Wajib berdomisili di Kutai Timur (dibuktikan dengan NIK/KTP Kutim), memiliki Nomor Induk Berusaha (NIB) aktif minimal 6 bulan, serta bukan berstatus ASN/TNI/Polri/Pegawai BUMN.
3. **Anti-Duplikasi Bantuan:** Pelaku usaha yang sudah menerima hibah permodalan APBD pada periode berjalan tidak dapat menerima hibah serupa secara berulang agar distribusi pembinaan merata.

### B. Pemetaan 5 Program Bantuan Ekosistem Kutai Timur pada Sistem
Output evaluasi dari aplikasi PATEN dikelompokkan langsung ke dalam 5 jalur intervensi nyata di Kutai Timur:
1. **Program Fasilitasi Legalitas Usaha Gratis (Diskop-UKM):** Diperuntukkan bagi UMKM yang aktif berjualan di event namun belum memiliki NIB, Sertifikasi Halal, P-IRT, atau HKI.
2. **Program Bantuan Usaha Produktif BAZNAS & TKM Kemnaker (Rp 5.000.000):** Diperuntukkan bagi usaha mikro rintisan/pemula dengan kapasitas transaksi awal yang membutuhkan suntikan modal dasar.
3. **Program Hibah Peralatan & Sarana Produksi (Pemkab Kutim / CSR):** Diperuntukkan bagi UMKM dengan frekuensi transaksi harian sangat tinggi (*fast-moving*) yang terkendala kapasitas produksi (butuh mesin kemasan, oven, gerobak, dll.).
4. **Program Hibah Permodalan APBD Pemkab Kutim (Rp 5.000.000 – Rp 25.000.000):** Diperuntukkan bagi Top Prioritas (kuota 200 UMKM/tahun) yang lolos penuh syarat Perbup (NIB aktif, 1 KK, belum pernah terima hibah) dan memiliki konsistensi performa niaga terbaik selama event.
5. **Program Kredit Usaha Bunga Rendah / KUR (BPR Kutim & Bankaltimtara) & Inkubasi CSR KPC:** Diperuntukkan bagi UMKM klaster mandiri dengan omzet besar dan adopsi QRIS tinggi yang membutuhkan ekspansi modal di atas plafon hibah APBD.

---

## 5. Indikator Keberhasilan Bisnis (Success Metrics / KPI)

* **Tingkat Partisipasi Pencatatan Stand:** $\ge 85\%$ stand terdaftar aktif mencatat transaksi harian menggunakan fitur POS Mini dan Awarding UMKM.
* **Kecepatan Input Transaksi:** Rata-rata waktu pencatatan per transaksi di bawah $3\text{ detik}$.
* **Akurasi Penjaringan Hibah:** $100\%$ kandidat rekomendasi Hibah Modal APBD yang dihasilkan modul SPK bebas dari duplikasi Nomor KK dan memenuhi syarat kelayakan dokumen dasar.
* **Adopsi Digitalisasi Pengunjung:** Jumlah pengunjung terdata (lewat e-kupon doorprize atau e-tiket HTM) mencapai $\ge 60\%$ dari estimasi kapasitas lapangan.