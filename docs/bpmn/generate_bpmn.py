#!/usr/bin/env python3
"""
Generator Script for PATEN End-to-End Business Process BPMN 2.0 XML Model.
Standar: OMG BPMN 2.0 (Fokus Murni Proses Bisnis / Business Process Tanpa Jargon Teknis)
"""

import xml.etree.ElementTree as ET

def generate_bpmn():
    bpmn_xml = '''<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL"
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI"
                  xmlns:dc="http://www.omg.org/spec/DD/20100524/DC"
                  xmlns:di="http://www.omg.org/spec/DD/20100524/DI"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:zeebe="http://camunda.org/schema/zeebe/1.0"
                  xmlns:modeler="http://camunda.org/schema/modeler/1.0"
                  id="Definitions_PATEN_E2E"
                  targetNamespace="http://bpmn.io/schema/bpmn"
                  exporter="Camunda Modeler"
                  exporterVersion="5.30.0"
                  modeler:executionPlatform="Camunda Cloud"
                  modeler:executionPlatformVersion="8.10.0">

  <bpmn:collaboration id="Collaboration_PATEN">
    <bpmn:participant id="Participant_PATEN"
                      name="Ekosistem Penyelenggaraan Bazar, Transaksi UMKM, dan Seleksi Bantuan Usaha Daerah"
                      processRef="Process_PATEN_E2E" />
    <bpmn:participant id="Participant_Colab"
                      name="Analisis Pemetaan &amp; Klasterisasi Potensi Usaha UMKM" />
    <bpmn:messageFlow id="MessageFlow_SyncOut"
                      name="Pengiriman Data Rekapitulasi Kinerja Stan"
                      sourceRef="Task_KirimWebhookColab"
                      targetRef="Participant_Colab" />
    <bpmn:messageFlow id="MessageFlow_SyncIn"
                      name="Hasil Pengelompokan Kategori Skala Usaha"
                      sourceRef="Participant_Colab"
                      targetRef="Task_KirimWebhookColab" />
  </bpmn:collaboration>

  <bpmn:process id="Process_PATEN_E2E" name="Alur Proses Bisnis Terpadu Penyelenggaraan Event dan Evaluasi Bantuan UMKM" isExecutable="true">
    <bpmn:laneSet id="LaneSet_PATEN">
      <bpmn:lane id="Lane_Panitia_Dinas" name="Panitia Penyelenggara &amp; Dinas Koperasi UKM Kutim">
        <bpmn:flowNodeRef>StartEvent_InisiasiAcara</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_InputDataEvent</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_TentukanKebutuhanStand</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_ScanTiketGerbang</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_PicuDoorprize</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_UmumkanPemenang</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_TriggerEvaluasiSpk</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_ReviewKeputusanDinas</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>EndEvent_KeputusanDiterbitkan</bpmn:flowNodeRef>
      </bpmn:lane>

      <bpmn:lane id="Lane_Publik" name="Pengunjung / Masyarakat (Konsumen)">
        <bpmn:flowNodeRef>Task_BukaMicrositePublik</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_TerimaTiketQr</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_MenikmatiBazar</bpmn:flowNodeRef>
      </bpmn:lane>

      <bpmn:lane id="Lane_UMKM_Kasir" name="Pelaku Usaha UMKM &amp; Kasir Stan">
        <bpmn:flowNodeRef>Task_RegistrasiProfilPerbup</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_LoginKasirPin</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_InputTransaksiPos</bpmn:flowNodeRef>
      </bpmn:lane>

      <bpmn:lane id="Lane_Sistem" name="Sistem Informasi PATEN">
        <bpmn:flowNodeRef>Task_GenerateEventToken</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_ProvisionStandsBatch</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Gateway_ForkOperasional</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_IssueTiketQr</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Gateway_VerifikasiTiket</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_TolakAksesGate</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>EndEvent_AksesDitolak</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_KonfirmasiKehadiran</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_EksekusiUndianAcak</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_SimpanProfilUmkm</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Gateway_ValidasiPin</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>EndEvent_KasirTerkunci</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_EksekusiTransaksiAtomik</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Gateway_LanjutTransaksi</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Gateway_JoinOperasional</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_HitungNormalisasiSaw</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_GuardrailPerbupKutim</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_KirimWebhookColab</bpmn:flowNodeRef>
        <bpmn:flowNodeRef>Task_MaskingDataPublik</bpmn:flowNodeRef>
      </bpmn:lane>
    </bpmn:laneSet>

    <!-- Node Definitions (Fokus Murni Probis) -->
    <bpmn:startEvent id="StartEvent_InisiasiAcara" name="Perencanaan Acara Bazar Dimulai">
      <bpmn:outgoing>Flow_01</bpmn:outgoing>
    </bpmn:startEvent>

    <bpmn:userTask id="Task_InputDataEvent" name="Menyusun Rencana Acara &amp; Kuota Pengunjung">
      <bpmn:incoming>Flow_01</bpmn:incoming>
      <bpmn:outgoing>Flow_02</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_GenerateEventToken" name="Menerbitkan Hak Akses Pengelolaan Acara">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-event-generator" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_02</bpmn:incoming>
      <bpmn:outgoing>Flow_03</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_TentukanKebutuhanStand" name="Menentukan Alokasi Stan &amp; Jadwal Kegiatan">
      <bpmn:incoming>Flow_03</bpmn:incoming>
      <bpmn:outgoing>Flow_04</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_ProvisionStandsBatch" name="Menerbitkan Penomoran Stan &amp; Kode Akses Kasir">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-stand-provisioner" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_04</bpmn:incoming>
      <bpmn:outgoing>Flow_05</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:parallelGateway id="Gateway_ForkOperasional" name="Pembagian Alur Publik &amp; Onboarding Stan">
      <bpmn:incoming>Flow_05</bpmn:incoming>
      <bpmn:outgoing>Flow_ToPublik</bpmn:outgoing>
      <bpmn:outgoing>Flow_ToUmkm</bpmn:outgoing>
    </bpmn:parallelGateway>

    <!-- Alur Publik & Pengunjung -->
    <bpmn:userTask id="Task_BukaMicrositePublik" name="Mendaftar Tiket Masuk Acara">
      <bpmn:incoming>Flow_ToPublik</bpmn:incoming>
      <bpmn:outgoing>Flow_P01</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_IssueTiketQr" name="Menerbitkan Tiket Masuk &amp; Nomor Undian Doorprize">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-ticket-issuer" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_P01</bpmn:incoming>
      <bpmn:outgoing>Flow_P02</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_TerimaTiketQr" name="Menerima Tiket Masuk &amp; Kupon Doorprize">
      <bpmn:incoming>Flow_P02</bpmn:incoming>
      <bpmn:outgoing>Flow_P03</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:userTask id="Task_ScanTiketGerbang" name="Memeriksa Tiket Masuk di Pintu Masuk">
      <bpmn:incoming>Flow_P03</bpmn:incoming>
      <bpmn:outgoing>Flow_P04</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:exclusiveGateway id="Gateway_VerifikasiTiket" name="Apakah Tiket Sah &amp; Belum Digunakan?" default="Flow_TiketValid">
      <bpmn:incoming>Flow_P04</bpmn:incoming>
      <bpmn:outgoing>Flow_TiketValid</bpmn:outgoing>
      <bpmn:outgoing>Flow_TiketDuplikat</bpmn:outgoing>
    </bpmn:exclusiveGateway>

    <bpmn:serviceTask id="Task_TolakAksesGate" name="Memberitahukan Tiket Tidak Sah / Sudah Dipakai">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-gate-verifier" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_TiketDuplikat</bpmn:incoming>
      <bpmn:outgoing>Flow_PReject</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:endEvent id="EndEvent_AksesDitolak" name="Akses Masuk Ditolak">
      <bpmn:incoming>Flow_PReject</bpmn:incoming>
    </bpmn:endEvent>

    <bpmn:serviceTask id="Task_KonfirmasiKehadiran" name="Mencatat Kehadiran Fisik Pengunjung">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-gate-verifier" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_TiketValid</bpmn:incoming>
      <bpmn:outgoing>Flow_P05</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_MenikmatiBazar" name="Berkunjung &amp; Berbelanja di Stan UMKM">
      <bpmn:incoming>Flow_P05</bpmn:incoming>
      <bpmn:outgoing>Flow_P06</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:userTask id="Task_PicuDoorprize" name="Melakukan Pengundian Doorprize">
      <bpmn:incoming>Flow_P06</bpmn:incoming>
      <bpmn:outgoing>Flow_P07</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_EksekusiUndianAcak" name="Menentukan Pemenang Khusus dari Pengunjung yang Hadir">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-doorprize-drawer" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_P07</bpmn:incoming>
      <bpmn:outgoing>Flow_P08</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_UmumkanPemenang" name="Menyerahkan Hadiah Doorprize">
      <bpmn:incoming>Flow_P08</bpmn:incoming>
      <bpmn:outgoing>Flow_JoinPublik</bpmn:outgoing>
    </bpmn:userTask>

    <!-- Alur Onboarding UMKM & Transaksi Penjualan -->
    <bpmn:userTask id="Task_RegistrasiProfilPerbup" name="Mendaftarkan Profil Usaha &amp; Kelengkapan Izin Usaha">
      <bpmn:incoming>Flow_ToUmkm</bpmn:incoming>
      <bpmn:outgoing>Flow_U01</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_SimpanProfilUmkm" name="Memverifikasi Identitas Usaha &amp; Daftar Produk">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-umkm-profiler" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_U01</bpmn:incoming>
      <bpmn:outgoing>Flow_U02</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_LoginKasirPin" name="Membuka Layanan Kasir Stan">
      <bpmn:incoming>Flow_U02</bpmn:incoming>
      <bpmn:outgoing>Flow_U03</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:exclusiveGateway id="Gateway_ValidasiPin" name="Apakah Kode Akses Stan Sesuai?" default="Flow_PinValid">
      <bpmn:incoming>Flow_U03</bpmn:incoming>
      <bpmn:outgoing>Flow_PinValid</bpmn:outgoing>
      <bpmn:outgoing>Flow_PinSalah</bpmn:outgoing>
    </bpmn:exclusiveGateway>

    <bpmn:endEvent id="EndEvent_KasirTerkunci" name="Akses Stan Ditolak">
      <bpmn:incoming>Flow_PinSalah</bpmn:incoming>
    </bpmn:endEvent>

    <bpmn:userTask id="Task_InputTransaksiPos" name="Melayani Pembelian Produk (Tunai / Non-Tunai)">
      <bpmn:incoming>Flow_PinValid</bpmn:incoming>
      <bpmn:incoming>Flow_LoopTransaksi</bpmn:incoming>
      <bpmn:outgoing>Flow_U04</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_EksekusiTransaksiAtomik" name="Mencatat Transaksi &amp; Menghitung Akumulasi Penjualan Stan">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-pos-processor" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_U04</bpmn:incoming>
      <bpmn:outgoing>Flow_U05</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:exclusiveGateway id="Gateway_LanjutTransaksi" name="Apakah Ada Pembelian Berikutnya?" default="Flow_SelesaiBazar">
      <bpmn:incoming>Flow_U05</bpmn:incoming>
      <bpmn:outgoing>Flow_LoopTransaksi</bpmn:outgoing>
      <bpmn:outgoing>Flow_SelesaiBazar</bpmn:outgoing>
    </bpmn:exclusiveGateway>

    <!-- Penutupan Bazar & Evaluasi Kebijakan Bantuan -->
    <bpmn:parallelGateway id="Gateway_JoinOperasional" name="Sinkronisasi Selesai Operasional Acara">
      <bpmn:incoming>Flow_JoinPublik</bpmn:incoming>
      <bpmn:incoming>Flow_SelesaiBazar</bpmn:incoming>
      <bpmn:outgoing>Flow_J01</bpmn:outgoing>
    </bpmn:parallelGateway>

    <bpmn:userTask id="Task_TriggerEvaluasiSpk" name="Memulai Evaluasi Kelayakan Bantuan Usaha UMKM">
      <bpmn:incoming>Flow_J01</bpmn:incoming>
      <bpmn:outgoing>Flow_E01</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:serviceTask id="Task_HitungNormalisasiSaw" name="Menilai Kinerja Penjualan, Keaktifan, Transaksi Digital &amp; Legalitas">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-spk-calculator" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_E01</bpmn:incoming>
      <bpmn:outgoing>Flow_E02</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:businessRuleTask id="Task_GuardrailPerbupKutim" name="Memverifikasi Syarat Bantuan: 1 KK 1 Bantuan, Wajib NIB, &amp; Bebas Duplikasi Bantuan">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-perbup-guardrail" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_E02</bpmn:incoming>
      <bpmn:outgoing>Flow_E03</bpmn:outgoing>
    </bpmn:businessRuleTask>

    <bpmn:serviceTask id="Task_KirimWebhookColab" name="Memetakan Klaster Usaha (Usaha Berkembang, Cepat, atau Rintisan)">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-ml-syncer" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_E03</bpmn:incoming>
      <bpmn:outgoing>Flow_E04</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:serviceTask id="Task_MaskingDataPublik" name="Menampilkan Papan Informasi Stan &amp; Menjaga Kerahasiaan Data Pribadi">
      <bpmn:extensionElements>
        <zeebe:taskDefinition type="paten-privacy-masker" />
      </bpmn:extensionElements>
      <bpmn:incoming>Flow_E04</bpmn:incoming>
      <bpmn:outgoing>Flow_E05</bpmn:outgoing>
    </bpmn:serviceTask>

    <bpmn:userTask id="Task_ReviewKeputusanDinas" name="Meninjau &amp; Menetapkan Rekomendasi Program Bantuan Modal">
      <bpmn:incoming>Flow_E05</bpmn:incoming>
      <bpmn:outgoing>Flow_E06</bpmn:outgoing>
    </bpmn:userTask>

    <bpmn:endEvent id="EndEvent_KeputusanDiterbitkan" name="Surat Keputusan (SK) Penerima Bantuan Usaha Diterbitkan">
      <bpmn:incoming>Flow_E06</bpmn:incoming>
    </bpmn:endEvent>

    <!-- Sequence Flows -->
    <bpmn:sequenceFlow id="Flow_01" sourceRef="StartEvent_InisiasiAcara" targetRef="Task_InputDataEvent" />
    <bpmn:sequenceFlow id="Flow_02" sourceRef="Task_InputDataEvent" targetRef="Task_GenerateEventToken" />
    <bpmn:sequenceFlow id="Flow_03" sourceRef="Task_GenerateEventToken" targetRef="Task_TentukanKebutuhanStand" />
    <bpmn:sequenceFlow id="Flow_04" sourceRef="Task_TentukanKebutuhanStand" targetRef="Task_ProvisionStandsBatch" />
    <bpmn:sequenceFlow id="Flow_05" sourceRef="Task_ProvisionStandsBatch" targetRef="Gateway_ForkOperasional" />

    <bpmn:sequenceFlow id="Flow_ToPublik" sourceRef="Gateway_ForkOperasional" targetRef="Task_BukaMicrositePublik" />
    <bpmn:sequenceFlow id="Flow_ToUmkm" sourceRef="Gateway_ForkOperasional" targetRef="Task_RegistrasiProfilPerbup" />

    <bpmn:sequenceFlow id="Flow_P01" sourceRef="Task_BukaMicrositePublik" targetRef="Task_IssueTiketQr" />
    <bpmn:sequenceFlow id="Flow_P02" sourceRef="Task_IssueTiketQr" targetRef="Task_TerimaTiketQr" />
    <bpmn:sequenceFlow id="Flow_P03" sourceRef="Task_TerimaTiketQr" targetRef="Task_ScanTiketGerbang" />
    <bpmn:sequenceFlow id="Flow_P04" sourceRef="Task_ScanTiketGerbang" targetRef="Gateway_VerifikasiTiket" />

    <bpmn:sequenceFlow id="Flow_TiketDuplikat" name="Tiket Tidak Sah / Sudah Digunakan" sourceRef="Gateway_VerifikasiTiket" targetRef="Task_TolakAksesGate">
      <bpmn:conditionExpression xsi:type="bpmn:tFormalExpression">=is_checked_in = true</bpmn:conditionExpression>
    </bpmn:sequenceFlow>
    <bpmn:sequenceFlow id="Flow_TiketValid" name="Tiket Sah &amp; Baru" sourceRef="Gateway_VerifikasiTiket" targetRef="Task_KonfirmasiKehadiran" />

    <bpmn:sequenceFlow id="Flow_PReject" sourceRef="Task_TolakAksesGate" targetRef="EndEvent_AksesDitolak" />
    <bpmn:sequenceFlow id="Flow_P05" sourceRef="Task_KonfirmasiKehadiran" targetRef="Task_MenikmatiBazar" />
    <bpmn:sequenceFlow id="Flow_P06" sourceRef="Task_MenikmatiBazar" targetRef="Task_PicuDoorprize" />
    <bpmn:sequenceFlow id="Flow_P07" sourceRef="Task_PicuDoorprize" targetRef="Task_EksekusiUndianAcak" />
    <bpmn:sequenceFlow id="Flow_P08" sourceRef="Task_EksekusiUndianAcak" targetRef="Task_UmumkanPemenang" />
    <bpmn:sequenceFlow id="Flow_JoinPublik" sourceRef="Task_UmumkanPemenang" targetRef="Gateway_JoinOperasional" />

    <bpmn:sequenceFlow id="Flow_U01" sourceRef="Task_RegistrasiProfilPerbup" targetRef="Task_SimpanProfilUmkm" />
    <bpmn:sequenceFlow id="Flow_U02" sourceRef="Task_SimpanProfilUmkm" targetRef="Task_LoginKasirPin" />
    <bpmn:sequenceFlow id="Flow_U03" sourceRef="Task_LoginKasirPin" targetRef="Gateway_ValidasiPin" />

    <bpmn:sequenceFlow id="Flow_PinSalah" name="Kode Akses Salah" sourceRef="Gateway_ValidasiPin" targetRef="EndEvent_KasirTerkunci">
      <bpmn:conditionExpression xsi:type="bpmn:tFormalExpression">=attempts &gt;= 10</bpmn:conditionExpression>
    </bpmn:sequenceFlow>
    <bpmn:sequenceFlow id="Flow_PinValid" name="Kode Akses Benar" sourceRef="Gateway_ValidasiPin" targetRef="Task_InputTransaksiPos" />

    <bpmn:sequenceFlow id="Flow_U04" sourceRef="Task_InputTransaksiPos" targetRef="Task_EksekusiTransaksiAtomik" />
    <bpmn:sequenceFlow id="Flow_U05" sourceRef="Task_EksekusiTransaksiAtomik" targetRef="Gateway_LanjutTransaksi" />

    <bpmn:sequenceFlow id="Flow_LoopTransaksi" name="Ada Pesanan Baru" sourceRef="Gateway_LanjutTransaksi" targetRef="Task_InputTransaksiPos">
      <bpmn:conditionExpression xsi:type="bpmn:tFormalExpression">=has_next_order = true</bpmn:conditionExpression>
    </bpmn:sequenceFlow>
    <bpmn:sequenceFlow id="Flow_SelesaiBazar" name="Bazar Berakhir" sourceRef="Gateway_LanjutTransaksi" targetRef="Gateway_JoinOperasional" />

    <bpmn:sequenceFlow id="Flow_J01" sourceRef="Gateway_JoinOperasional" targetRef="Task_TriggerEvaluasiSpk" />
    <bpmn:sequenceFlow id="Flow_E01" sourceRef="Task_TriggerEvaluasiSpk" targetRef="Task_HitungNormalisasiSaw" />
    <bpmn:sequenceFlow id="Flow_E02" sourceRef="Task_HitungNormalisasiSaw" targetRef="Task_GuardrailPerbupKutim" />
    <bpmn:sequenceFlow id="Flow_E03" sourceRef="Task_GuardrailPerbupKutim" targetRef="Task_KirimWebhookColab" />
    <bpmn:sequenceFlow id="Flow_E04" sourceRef="Task_KirimWebhookColab" targetRef="Task_MaskingDataPublik" />
    <bpmn:sequenceFlow id="Flow_E05" sourceRef="Task_MaskingDataPublik" targetRef="Task_ReviewKeputusanDinas" />
    <bpmn:sequenceFlow id="Flow_E06" sourceRef="Task_ReviewKeputusanDinas" targetRef="EndEvent_KeputusanDiterbitkan" />

  </bpmn:process>

  <bpmndi:BPMNDiagram id="BPMNDiagram_PATEN">
    <bpmndi:BPMNPlane id="BPMNPlane_PATEN" bpmnElement="Collaboration_PATEN">
      <!-- Participant PATEN Pool -->
      <bpmndi:BPMNShape id="Participant_PATEN_di" bpmnElement="Participant_PATEN" isHorizontal="true">
        <dc:Bounds x="160" y="80" width="3100" height="1140" />
      </bpmndi:BPMNShape>

      <!-- Lanes -->
      <bpmndi:BPMNShape id="Lane_Panitia_Dinas_di" bpmnElement="Lane_Panitia_Dinas" isHorizontal="true">
        <dc:Bounds x="190" y="80" width="3070" height="260" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="Lane_Publik_di" bpmnElement="Lane_Publik" isHorizontal="true">
        <dc:Bounds x="190" y="340" width="3070" height="240" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="Lane_UMKM_Kasir_di" bpmnElement="Lane_UMKM_Kasir" isHorizontal="true">
        <dc:Bounds x="190" y="580" width="3070" height="260" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="Lane_Sistem_di" bpmnElement="Lane_Sistem" isHorizontal="true">
        <dc:Bounds x="190" y="840" width="3070" height="380" />
      </bpmndi:BPMNShape>

      <!-- Participant Colab External Pool -->
      <bpmndi:BPMNShape id="Participant_Colab_di" bpmnElement="Participant_Colab" isHorizontal="true">
        <dc:Bounds x="2500" y="1260" width="450" height="120" />
      </bpmndi:BPMNShape>

      <!-- Stage 1 Shapes (Inisiasi) -->
      <bpmndi:BPMNShape id="StartEvent_InisiasiAcara_di" bpmnElement="StartEvent_InisiasiAcara">
        <dc:Bounds x="250" y="172" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="226" y="215" width="85" height="40" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_InputDataEvent_di" bpmnElement="Task_InputDataEvent">
        <dc:Bounds x="340" y="150" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_GenerateEventToken_di" bpmnElement="Task_GenerateEventToken">
        <dc:Bounds x="340" y="930" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_TentukanKebutuhanStand_di" bpmnElement="Task_TentukanKebutuhanStand">
        <dc:Bounds x="520" y="150" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_ProvisionStandsBatch_di" bpmnElement="Task_ProvisionStandsBatch">
        <dc:Bounds x="520" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Gateway_ForkOperasional_di" bpmnElement="Gateway_ForkOperasional">
        <dc:Bounds x="710" y="945" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="693" y="1005" width="85" height="40" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <!-- Stage 2 & 3 Shapes (Publik & Gate) -->
      <bpmndi:BPMNShape id="Task_BukaMicrositePublik_di" bpmnElement="Task_BukaMicrositePublik">
        <dc:Bounds x="810" y="410" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_IssueTiketQr_di" bpmnElement="Task_IssueTiketQr">
        <dc:Bounds x="810" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_TerimaTiketQr_di" bpmnElement="Task_TerimaTiketQr">
        <dc:Bounds x="1000" y="410" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_ScanTiketGerbang_di" bpmnElement="Task_ScanTiketGerbang">
        <dc:Bounds x="1000" y="150" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Gateway_VerifikasiTiket_di" bpmnElement="Gateway_VerifikasiTiket" isMarkerVisible="true">
        <dc:Bounds x="1180" y="945" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1165" y="907.5" width="80" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_TolakAksesGate_di" bpmnElement="Task_TolakAksesGate">
        <dc:Bounds x="1140" y="1060" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="EndEvent_AksesDitolak_di" bpmnElement="EndEvent_AksesDitolak">
        <dc:Bounds x="1187" y="1172" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1168" y="1215" width="75" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_KonfirmasiKehadiran_di" bpmnElement="Task_KonfirmasiKehadiran">
        <dc:Bounds x="1290" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_MenikmatiBazar_di" bpmnElement="Task_MenikmatiBazar">
        <dc:Bounds x="1290" y="410" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_PicuDoorprize_di" bpmnElement="Task_PicuDoorprize">
        <dc:Bounds x="1470" y="150" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_EksekusiUndianAcak_di" bpmnElement="Task_EksekusiUndianAcak">
        <dc:Bounds x="1470" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_UmumkanPemenang_di" bpmnElement="Task_UmumkanPemenang">
        <dc:Bounds x="1650" y="150" width="130" height="80" />
      </bpmndi:BPMNShape>

      <!-- Stage 4 Shapes (UMKM & POS) -->
      <bpmndi:BPMNShape id="Task_RegistrasiProfilPerbup_di" bpmnElement="Task_RegistrasiProfilPerbup">
        <dc:Bounds x="810" y="660" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_SimpanProfilUmkm_di" bpmnElement="Task_SimpanProfilUmkm">
        <dc:Bounds x="990" y="930" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_LoginKasirPin_di" bpmnElement="Task_LoginKasirPin">
        <dc:Bounds x="1160" y="660" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Gateway_ValidasiPin_di" bpmnElement="Gateway_ValidasiPin" isMarkerVisible="true">
        <dc:Bounds x="1335" y="675" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1320" y="638" width="81" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="EndEvent_KasirTerkunci_di" bpmnElement="EndEvent_KasirTerkunci">
        <dc:Bounds x="1342" y="782" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1323" y="825" width="75" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_InputTransaksiPos_di" bpmnElement="Task_InputTransaksiPos">
        <dc:Bounds x="1440" y="660" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_EksekusiTransaksiAtomik_di" bpmnElement="Task_EksekusiTransaksiAtomik">
        <dc:Bounds x="1440" y="930" width="130" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Gateway_LanjutTransaksi_di" bpmnElement="Gateway_LanjutTransaksi" isMarkerVisible="true">
        <dc:Bounds x="1625" y="945" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1612" y="1005" width="77" height="40" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <!-- Join Shapes -->
      <bpmndi:BPMNShape id="Gateway_JoinOperasional_di" bpmnElement="Gateway_JoinOperasional">
        <dc:Bounds x="1835" y="945" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1818" y="1005" width="85" height="40" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <!-- Stage 5 Shapes (SPK & Colab) -->
      <bpmndi:BPMNShape id="Task_TriggerEvaluasiSpk_di" bpmnElement="Task_TriggerEvaluasiSpk">
        <dc:Bounds x="1930" y="150" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_HitungNormalisasiSaw_di" bpmnElement="Task_HitungNormalisasiSaw">
        <dc:Bounds x="1930" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_GuardrailPerbupKutim_di" bpmnElement="Task_GuardrailPerbupKutim">
        <dc:Bounds x="2120" y="930" width="150" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_KirimWebhookColab_di" bpmnElement="Task_KirimWebhookColab">
        <dc:Bounds x="2320" y="930" width="150" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_MaskingDataPublik_di" bpmnElement="Task_MaskingDataPublik">
        <dc:Bounds x="2520" y="930" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="Task_ReviewKeputusanDinas_di" bpmnElement="Task_ReviewKeputusanDinas">
        <dc:Bounds x="2710" y="150" width="140" height="80" />
      </bpmndi:BPMNShape>

      <bpmndi:BPMNShape id="EndEvent_KeputusanDiterbitkan_di" bpmnElement="EndEvent_KeputusanDiterbitkan">
        <dc:Bounds x="2912" y="172" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="2890" y="215" width="81" height="66" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>

      <!-- Edge Waypoints -->
      <!-- Inisiasi -->
      <bpmndi:BPMNEdge id="Flow_01_di" bpmnElement="Flow_01">
        <di:waypoint x="286" y="190" />
        <di:waypoint x="340" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_02_di" bpmnElement="Flow_02">
        <di:waypoint x="405" y="230" />
        <di:waypoint x="405" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_03_di" bpmnElement="Flow_03">
        <di:waypoint x="470" y="970" />
        <di:waypoint x="495" y="970" />
        <di:waypoint x="495" y="190" />
        <di:waypoint x="520" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_04_di" bpmnElement="Flow_04">
        <di:waypoint x="590" y="230" />
        <di:waypoint x="590" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_05_di" bpmnElement="Flow_05">
        <di:waypoint x="660" y="970" />
        <di:waypoint x="710" y="970" />
      </bpmndi:BPMNEdge>

      <!-- Fork -->
      <bpmndi:BPMNEdge id="Flow_ToPublik_di" bpmnElement="Flow_ToPublik">
        <di:waypoint x="735" y="945" />
        <di:waypoint x="735" y="450" />
        <di:waypoint x="810" y="450" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_ToUmkm_di" bpmnElement="Flow_ToUmkm">
        <di:waypoint x="735" y="945" />
        <di:waypoint x="735" y="700" />
        <di:waypoint x="810" y="700" />
      </bpmndi:BPMNEdge>

      <!-- Publik Branch -->
      <bpmndi:BPMNEdge id="Flow_P01_di" bpmnElement="Flow_P01">
        <di:waypoint x="880" y="490" />
        <di:waypoint x="880" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P02_di" bpmnElement="Flow_P02">
        <di:waypoint x="950" y="970" />
        <di:waypoint x="975" y="970" />
        <di:waypoint x="975" y="450" />
        <di:waypoint x="1000" y="450" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P03_di" bpmnElement="Flow_P03">
        <di:waypoint x="1065" y="410" />
        <di:waypoint x="1065" y="230" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P04_di" bpmnElement="Flow_P04">
        <di:waypoint x="1130" y="190" />
        <di:waypoint x="1155" y="190" />
        <di:waypoint x="1155" y="970" />
        <di:waypoint x="1180" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_TiketValid_di" bpmnElement="Flow_TiketValid">
        <di:waypoint x="1230" y="970" />
        <di:waypoint x="1290" y="970" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1225" y="952" width="72" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_TiketDuplikat_di" bpmnElement="Flow_TiketDuplikat">
        <di:waypoint x="1205" y="995" />
        <di:waypoint x="1205" y="1060" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1210" y="1020" width="86" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_PReject_di" bpmnElement="Flow_PReject">
        <di:waypoint x="1205" y="1140" />
        <di:waypoint x="1205" y="1172" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P05_di" bpmnElement="Flow_P05">
        <di:waypoint x="1360" y="930" />
        <di:waypoint x="1360" y="490" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P06_di" bpmnElement="Flow_P06">
        <di:waypoint x="1430" y="450" />
        <di:waypoint x="1450" y="450" />
        <di:waypoint x="1450" y="190" />
        <di:waypoint x="1470" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P07_di" bpmnElement="Flow_P07">
        <di:waypoint x="1540" y="230" />
        <di:waypoint x="1540" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_P08_di" bpmnElement="Flow_P08">
        <di:waypoint x="1610" y="970" />
        <di:waypoint x="1630" y="970" />
        <di:waypoint x="1630" y="190" />
        <di:waypoint x="1650" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_JoinPublik_di" bpmnElement="Flow_JoinPublik">
        <di:waypoint x="1780" y="190" />
        <di:waypoint x="1860" y="190" />
        <di:waypoint x="1860" y="945" />
      </bpmndi:BPMNEdge>

      <!-- UMKM Branch -->
      <bpmndi:BPMNEdge id="Flow_U01_di" bpmnElement="Flow_U01">
        <di:waypoint x="950" y="700" />
        <di:waypoint x="970" y="700" />
        <di:waypoint x="970" y="970" />
        <di:waypoint x="990" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_U02_di" bpmnElement="Flow_U02">
        <di:waypoint x="1120" y="970" />
        <di:waypoint x="1140" y="970" />
        <di:waypoint x="1140" y="700" />
        <di:waypoint x="1160" y="700" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_U03_di" bpmnElement="Flow_U03">
        <di:waypoint x="1290" y="700" />
        <di:waypoint x="1335" y="700" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_PinSalah_di" bpmnElement="Flow_PinSalah">
        <di:waypoint x="1360" y="725" />
        <di:waypoint x="1360" y="782" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1366" y="743" width="88" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_PinValid_di" bpmnElement="Flow_PinValid">
        <di:waypoint x="1385" y="700" />
        <di:waypoint x="1440" y="700" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1382" y="682" width="60" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_U04_di" bpmnElement="Flow_U04">
        <di:waypoint x="1505" y="740" />
        <di:waypoint x="1505" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_U05_di" bpmnElement="Flow_U05">
        <di:waypoint x="1570" y="970" />
        <di:waypoint x="1625" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_LoopTransaksi_di" bpmnElement="Flow_LoopTransaksi">
        <di:waypoint x="1650" y="945" />
        <di:waypoint x="1650" y="680" />
        <di:waypoint x="1570" y="680" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1654" y="793" width="90" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_SelesaiBazar_di" bpmnElement="Flow_SelesaiBazar">
        <di:waypoint x="1675" y="970" />
        <di:waypoint x="1835" y="970" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="1722" y="952" width="73" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>

      <!-- SPK & Colab Flows -->
      <bpmndi:BPMNEdge id="Flow_J01_di" bpmnElement="Flow_J01">
        <di:waypoint x="1885" y="970" />
        <di:waypoint x="1908" y="970" />
        <di:waypoint x="1908" y="190" />
        <di:waypoint x="1930" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E01_di" bpmnElement="Flow_E01">
        <di:waypoint x="2000" y="230" />
        <di:waypoint x="2000" y="930" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E02_di" bpmnElement="Flow_E02">
        <di:waypoint x="2070" y="970" />
        <di:waypoint x="2120" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E03_di" bpmnElement="Flow_E03">
        <di:waypoint x="2270" y="970" />
        <di:waypoint x="2320" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E04_di" bpmnElement="Flow_E04">
        <di:waypoint x="2470" y="970" />
        <di:waypoint x="2520" y="970" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E05_di" bpmnElement="Flow_E05">
        <di:waypoint x="2660" y="970" />
        <di:waypoint x="2685" y="970" />
        <di:waypoint x="2685" y="190" />
        <di:waypoint x="2710" y="190" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_E06_di" bpmnElement="Flow_E06">
        <di:waypoint x="2850" y="190" />
        <di:waypoint x="2912" y="190" />
      </bpmndi:BPMNEdge>

      <!-- Message Flows -->
      <bpmndi:BPMNEdge id="MessageFlow_SyncOut_di" bpmnElement="MessageFlow_SyncOut">
        <di:waypoint x="2395" y="1010" />
        <di:waypoint x="2395" y="1320" />
        <di:waypoint x="2500" y="1320" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="2388" y="1165" width="84" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="MessageFlow_SyncIn_di" bpmnElement="MessageFlow_SyncIn">
        <di:waypoint x="2650" y="1260" />
        <di:waypoint x="2650" y="1090" />
        <di:waypoint x="2440" y="1090" />
        <di:waypoint x="2440" y="1010" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="2558" y="1095" width="84" height="40" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>

    </bpmndi:BPMNPlane>
  </bpmndi:BPMNDiagram>
</bpmn:definitions>
'''
    return bpmn_xml

if __name__ == '__main__':
    content = generate_bpmn()
    try:
        ET.fromstring(content)
        print("BPMN 2.0 XML Syntax: VALID!")
    except Exception as e:
        print(f"BPMN XML Syntax Error: {e}")
        exit(1)

    target_path = 'docs/bpmn/paten_business_process_e2e.bpmn'
    with open(target_path, 'w', encoding='utf-8') as f:
        f.write(content.strip() + '\n')
    print(f"Generated {target_path} successfully.")
