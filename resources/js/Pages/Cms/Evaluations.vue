<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    token: String,
    evaluations: Array,
    summary: Object,
    cms_url: String,
});

const isEvaluating = ref(false);
const statusMessage = ref('');
const errorMessage = ref('');

const runEvaluation = () => {
    isEvaluating.value = true;
    statusMessage.value = '';
    errorMessage.value = '';

    router.post(`/cms/${props.event.slug}/evaluate-spk?token=${props.token}`, {}, {
        onSuccess: () => {
            statusMessage.value = 'Evaluasi SPK dan verifikasi Guardrail Perbup berhasil dieksekusi!';
            isEvaluating.value = false;
        },
        onError: (errs) => {
            errorMessage.value = 'Gagal mengeksekusi evaluasi: ' + Object.values(errs).join(', ');
            isEvaluating.value = false;
        },
    });
};
</script>

<template>
    <div>
        <h1>Laporan Evaluasi SPK & Rekomendasi Bantuan UMKM</h1>
        <p><strong>Nama Event:</strong> {{ event.nama_event }}</p>
        <p><strong>Penyelenggara:</strong> {{ event.penyelenggara }} | <strong>Lokasi:</strong> {{ event.lokasi_venue }}</p>
        <p>
            <a :href="`/cms/${event.slug}?token=${token}`">[Kembali ke Dashboard Utama CMS]</a>
            &nbsp;|&nbsp;
            <a :href="`/e/${event.slug}/leaderboard`" target="_blank">[Buka Klasemen Leaderboard Publik]</a>
        </p>

        <hr />

        <div v-if="statusMessage">
            <p><strong>[STATUS SUKSES]:</strong> {{ statusMessage }}</p>
        </div>
        <div v-if="errorMessage">
            <p><strong>[STATUS ERROR]:</strong> {{ errorMessage }}</p>
        </div>

        <p>
            <button type="button" @click="runEvaluation" :disabled="isEvaluating">
                {{ isEvaluating ? 'Sedang Memproses Algoritma SPK...' : 'Jalankan Evaluasi SPK & Guardrail Perbup' }}
            </button>
        </p>

        <hr />

        <h2>Ringkasan Distribusi Program Bantuan Pemkab Kutim</h2>
        <table border="1" cellpadding="6" cellspacing="0" v-if="summary">
            <thead>
                <tr>
                    <th>Total Stand Dievaluasi</th>
                    <th>Prioritas Hibah Modal APBD (Kuota 200)</th>
                    <th>Fasilitasi Legalitas (NIB/Halal)</th>
                    <th>Hibah Alat Produksi</th>
                    <th>Fasilitasi Kredit Perbankan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ summary.total_evaluated }} Stand</td>
                    <td><strong>{{ summary.prioritas_hibah_200 }} Pelaku Usaha</strong></td>
                    <td>{{ summary.fasilitasi_legalitas }} Pelaku Usaha</td>
                    <td>{{ summary.hibah_alat }} Pelaku Usaha</td>
                    <td>{{ summary.kredit_perbankan }} Pelaku Usaha</td>
                </tr>
            </tbody>
        </table>

        <hr />

        <h2>Rekapitulasi Lengkap Nilai SPK & Guardrail Regulasi (Internal Dinas)</h2>
        <table border="1" cellpadding="6" cellspacing="0" v-if="evaluations && evaluations.length">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Stand</th>
                    <th>Nama Usaha</th>
                    <th>Nama Pemilik</th>
                    <th>Nomor KK</th>
                    <th>Skor Konsistensi</th>
                    <th>Skor QRIS</th>
                    <th>Skor Legalitas</th>
                    <th>Skor Akhir SAW</th>
                    <th>Klaster ML</th>
                    <th>Rekomendasi Bantuan</th>
                    <th>Prioritas APBD</th>
                    <th>Catatan Dinas / Alasan Regulasi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(ev, idx) in evaluations" :key="ev.id">
                    <td>{{ idx + 1 }}</td>
                    <td><strong>{{ ev.stand ? ev.stand.nomor_stand : '-' }}</strong></td>
                    <td>{{ ev.stand && ev.stand.umkm_profile ? ev.stand.umkm_profile.nama_usaha : '-' }}</td>
                    <td>{{ ev.stand && ev.stand.umkm_profile ? ev.stand.umkm_profile.nama_pemilik : '-' }}</td>
                    <td><code>{{ ev.stand && ev.stand.umkm_profile ? ev.stand.umkm_profile.no_kk : '-' }}</code></td>
                    <td>{{ ev.score_konsistensi_transaksi }}</td>
                    <td>{{ (ev.score_digitalisasi_qris * 100).toFixed(1) }}%</td>
                    <td>{{ ev.score_kepatuhan_regulasi }}</td>
                    <td><strong>{{ ev.skor_akhir_spk }}</strong></td>
                    <td>{{ ev.cluster_kategori || '-' }}</td>
                    <td>
                        <strong>{{ ev.rekomendasi_program.toUpperCase().replace(/_/g, ' ') }}</strong>
                    </td>
                    <td>
                        {{ ev.is_prioritas_hibah_200 ? 'PRIORITAS (LOLOS)' : 'TIDAK' }}
                    </td>
                    <td>{{ ev.catatan_dinas || '-' }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else>
            <em>Belum ada data evaluasi. Silakan klik tombol "Jalankan Evaluasi SPK & Guardrail Perbup" di atas.</em>
        </p>
    </div>
</template>
