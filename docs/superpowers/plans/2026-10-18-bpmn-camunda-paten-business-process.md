# BPMN 2.0 Camunda Modeler End-to-End Business Process Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menghasilkan model proses bisnis BPMN 2.0 standar Camunda 8 enterprise yang komprehensif, valid, dan berkoordinat rapi untuk proyek PATEN, mendistribusikannya ke direktori repositori `docs/bpmn/`, serta mendeploy-nya langsung ke cluster Camunda 8 lokal yang sedang berjalan.

**Architecture:** Membangun *Unified Master Collaboration Diagram* yang memuat 4 Pools & Swimlanes (Publik, UMKM/Kasir, Panitia & Dinas KUKM, Sistem PATEN Backend) mencakup 5 siklus hidup event. Model dilengkapi elemen semantik Camunda 8 (Zeebe job definitions, FEEL expressions, task types `serviceTask`, `userTask`, `businessRuleTask`) dan diagram interchange visual (`bpmndi`) presisi untuk Camunda Desktop Modeler.

**Tech Stack:** OMG BPMN 2.0 XML (ISO/IEC 19510), Camunda 8.10 REST API v2, Camunda Modeler v5.x, Zeebe Engine, cURL, Python 3 XML validator.

**Spec:** [docs/superpowers/specs/2026-10-18-bpmn-camunda-paten-business-process-design.md](file:///Users/yusuf/Documents/Project/PATEN/docs/superpowers/specs/2026-10-18-bpmn-camunda-paten-business-process-design.md)

## Global Constraints

- Standar format: OMG BPMN 2.0 dengan namespace resmi `http://www.omg.org/spec/BPMN/20100524/MODEL` dan Camunda Zeebe `http://camunda.org/schema/zeebe/1.0`.
- Target lokasi file BPMN: `docs/bpmn/paten_business_process_e2e.bpmn`.
- Diagram Interchange (`bpmndi`): Setiap node shape dan edge sequence flow wajib memiliki koordinat $(x,y)$ dan waypoints valid agar dapat dibuka tanpa galat tata letak di Camunda Modeler.
- Penegakan regulasi Perbup Kutim: Model wajib memuat business rules: 1 KK 1 Penerima Bantuan APBD, verifikasi NIB, dan seleksi anti-duplikasi penerima hibah modal masa lalu.
- Transaksi POS atomik: Model wajib merefleksikan Service Task dengan penguncian baris `lockForUpdate()`.

---

### Task 1: Generate Master BPMN 2.0 XML Engine Model & Diagram Interchange

**Files:**
- Create: `docs/bpmn/paten_business_process_e2e.bpmn`
- Test: `tests/Unit/BpmnXmlValidationTest.php` atau skrip verifikasi XML sintaksis.

**Interfaces:**
- Consumes: Spesifikasi desain di `docs/superpowers/specs/2026-10-18-bpmn-camunda-paten-business-process-design.md`.
- Produces: File BPMN 2.0 XML terstruktur lengkap dengan koordinat grafis `bpmndi:BPMNDiagram` untuk 4 Pools dan interaksi message flows.

- [ ] **Step 1: Buat direktori tujuan `docs/bpmn`**
- [ ] **Step 2: Susun file BPMN 2.0 XML `docs/bpmn/paten_business_process_e2e.bpmn`**
  - Definisikan `<bpmn:collaboration id="Collaboration_PATEN_E2E">`.
  - Definisikan 4 `<bpmn:participant>`:
    * `Participant_Publik` (Pengunjung / Publik)
    * `Participant_UMKM_Kasir` (Pelaku Usaha UMKM & Kasir Stand)
    * `Participant_Panitia_Dinas` (Panitia Penyelenggara & Dinas Koperasi UKM)
    * `Participant_Sistem_PATEN` (Sistem PATEN Backend Engine)
  - Susun elemen `<bpmn:process id="Process_PATEN_E2E" name="Sistem PATEN - Proses Bisnis End-to-End" isExecutable="true">` yang berisi start events, user tasks, service tasks (dengan `zeebe:taskDefinition`), business rule tasks, exclusive gateways, parallel gateways, dan end events.
  - Tambahkan seluruh koordinat grafis visual lengkap di `<bpmndi:BPMNDiagram id="BPMNDiagram_PATEN_E2E">` untuk seluruh pools, shapes, waypoints sequence flows, dan message flows.
- [ ] **Step 3: Validasi sintaksis dan integritas XML**
  - Jalankan parser XML Python / PHP untuk memverifikasi struktur tag XML tertutup sempurna tanpa error parsing.
- [ ] **Step 4: Commit file BPMN ke git**
  - `git add docs/bpmn/paten_business_process_e2e.bpmn && git commit -m "feat(bpmn): add end-to-end PATEN business process BPMN 2.0 model"`

---

### Task 2: Automated Deployment ke Camunda 8 Engine Lokal via REST API

**Files:**
- Source: `docs/bpmn/paten_business_process_e2e.bpmn`
- Command: `curl -X POST -F "resources=@docs/bpmn/paten_business_process_e2e.bpmn" http://localhost:8080/v2/deployments`

**Interfaces:**
- Consumes: `docs/bpmn/paten_business_process_e2e.bpmn`.
- Produces: `deploymentKey`, `processDefinitionKey`, dan `processDefinitionId: "Process_PATEN_E2E"` terdaftar di Zeebe engine.

- [ ] **Step 1: Kirim request deployment via curl multipart form data**
- [ ] **Step 2: Verifikasi HTTP 200 OK dengan status deployment aktif**
- [ ] **Step 3: Pastikan processDefinitionKey dan version 1 berhasil dialokasikan oleh Zeebe**

---

### Task 3: Verifikasi Deployment via Camunda MCP Tools & Engine

**Files:**
- MCP Server: `camunda`
- Tools: `searchProcessDefinitions`, `getProcessDefinitionXml`

**Interfaces:**
- Consumes: Zeebe state di cluster `localhost:26501` / `localhost:8080`.
- Produces: Bukti verifikasi aktif dari Camunda MCP.

- [ ] **Step 1: Panggil tool MCP `searchProcessDefinitions` dengan filter `Process_PATEN_E2E`**
- [ ] **Step 2: Panggil tool MCP `getProcessDefinitionXml` menggunakan key yang dihasilkan untuk memastikan XML tersimpan secara presisi di engine**
- [ ] **Step 3: Verifikasi status definition `ACTIVE`**

---

### Task 4: Dokumentasi Panduan System Analyst & Integrasi Camunda Modeler

**Files:**
- Create: `docs/bpmn/README.md`

**Interfaces:**
- Consumes: Detail spesifikasi teknis dan deployment.
- Produces: Dokumen operasional panduan bagi System Analyst, Business Process Specialist, dan Tim Pengembang.

- [ ] **Step 1: Tulis dokumentasi teknis `docs/bpmn/README.md`**
  - Penjelasan arsitektur pools & lanes.
  - Kamus variabel proses dan payload data Zeebe.
  - Panduan membuka & mengedit di Camunda Desktop Modeler (`/Applications/Camunda Modeler.app`).
  - Panduan pengujian instance proses melalui Camunda Tasklist, Operate, dan MCP.
- [ ] **Step 2: Commit dokumentasi ke git**
  - `git add docs/bpmn/README.md && git commit -m "docs(bpmn): add comprehensive System Analyst guide for PATEN BPMN 2.0"`
