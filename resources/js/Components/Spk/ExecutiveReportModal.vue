<script setup>
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    event: {
        type: Object,
        required: true,
    },
    evaluations: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const handlePrint = () => {
    if (typeof window !== 'undefined') {
        window.print();
    }
};

const formatProgramName = (prog) => {
    switch (prog) {
        case 'hibah_modal_apbd': return 'Hibah Modal APBD (Prioritas 200)';
        case 'hibah_alat': return 'Hibah Alat Produksi Usaha';
        case 'kredit_perbankan': return 'Fasilitasi Kredit BPR / KUR';
        case 'fasilitasi_legalitas': return 'Fasilitasi NIB / Halal / P-IRT';
        default: return prog ? prog.toUpperCase().replace(/_/g, ' ') : '-';
    }
};
</script>

<template>
    <BaseModal
        :show="show"
        title="Laporan Eksekutif Berita Acara Rekapitulasi SPK"
        max-width="2xl"
        @close="$emit('close')"
    >
        <div class="space-y-6 print:p-0">
            <!-- Official Government Letterhead (Kop Surat Dinas) -->
            <div class="border-b-2 border-[#132952] pb-4 text-center">
                <p class="font-mono text-xs uppercase tracking-widest font-bold text-[#132952]">
                    PEMERINTAH KABUPATEN KUTAI TIMUR
                </p>
                <p class="font-display text-lg font-bold text-[#132952] uppercase">
                    DINAS KOPERASI DAN USAHA KECIL MENENGAH
                </p>
                <p class="text-[11px] font-sans text-[#64748B]">
                    Kawasan Pusat Perkantoran Bukit Pelangi, Sangatta, Kutai Timur, Kalimantan Timur
                </p>
                <div class="mt-2 h-0.5 bg-[#132952] w-full"></div>
            </div>

            <!-- Title & Metadata -->
            <div class="text-center space-y-1">
                <h4 class="font-mono text-xs uppercase font-bold tracking-wider text-[#132952]">
                    BERITA ACARA HASIL SELEKSI DAN EVALUASI SPK
                </h4>
                <p class="text-xs font-semibold text-[#5B6B8A]">
                    NOMINASI PENERIMA PROGRAM BANTUAN UMKM & HIBAH MODAL APBD TAHUN 2026
                </p>
                <p class="text-[11px] font-mono text-[#64748B]">
                    Event: {{ event.nama_event }} &middot; Tanggal: {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
            </div>

            <!-- Summary Table -->
            <div class="p-3 rounded-[6px] bg-[#F8FAFC] border border-[#CBD5E1] text-xs font-mono">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                    <div>
                        <span class="text-[#64748B] text-[10px] block">TOTAL EVALUASI</span>
                        <strong class="text-sm text-[#132952]">{{ summary.total_evaluated || 0 }} Stand</strong>
                    </div>
                    <div>
                        <span class="text-[#B45309] text-[10px] block font-bold">KUOTA 200 APBD</span>
                        <strong class="text-sm text-[#B45309]">{{ summary.prioritas_hibah_200 || 0 }} UMKM</strong>
                    </div>
                    <div>
                        <span class="text-[#1D4ED8] text-[10px] block">HIBAH ALAT</span>
                        <strong class="text-sm text-[#132952]">{{ summary.hibah_alat || 0 }} UMKM</strong>
                    </div>
                    <div>
                        <span class="text-[#00B262] text-[10px] block">KREDIT PERBANKAN</span>
                        <strong class="text-sm text-[#132952]">{{ summary.kredit_perbankan || 0 }} UMKM</strong>
                    </div>
                </div>
            </div>

            <!-- Nominations Table -->
            <div class="border border-[#CBD5E1] rounded-[4px] overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-[#F1F5F9] border-b border-[#CBD5E1] font-mono text-[10px] text-[#64748B] uppercase">
                            <th class="py-2 px-3 w-10 text-center">Rank</th>
                            <th class="py-2 px-3">Stand & UMKM</th>
                            <th class="py-2 px-3">Pemilik Usaha</th>
                            <th class="py-2 px-3 text-center">Skor SAW</th>
                            <th class="py-2 px-3">Rekomendasi Program</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] text-[11px]">
                        <tr
                            v-for="(ev, idx) in evaluations.slice(0, 20)"
                            :key="ev.id"
                            :class="ev.is_prioritas_hibah_200 ? 'bg-[#FFFBEB]/50' : ''"
                        >
                            <td class="py-2 px-3 font-mono text-center font-bold">
                                #{{ idx + 1 }}
                            </td>
                            <td class="py-2 px-3 font-medium text-[#132952]">
                                {{ ev.stand?.nomor_stand }} - {{ ev.umkm_profile?.nama_usaha }}
                            </td>
                            <td class="py-2 px-3 text-[#64748B]">
                                {{ ev.umkm_profile?.nama_pemilik }}
                            </td>
                            <td class="py-2 px-3 font-mono font-bold text-center text-[#132952]">
                                {{ ev.skor_akhir_spk }}
                            </td>
                            <td class="py-2 px-3 font-mono">
                                <span :class="ev.is_prioritas_hibah_200 ? 'text-[#B45309] font-bold' : 'text-[#132952]'">
                                    {{ formatProgramName(ev.rekomendasi_program) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures Section -->
            <div class="pt-6 grid grid-cols-2 gap-8 text-center text-xs font-sans">
                <div class="space-y-16">
                    <p class="font-mono text-[11px] text-[#64748B]">
                        Tim Penilai Teknis SPK PATEN,<br>
                        Kabupaten Kutai Timur
                    </p>
                    <div>
                        <p class="font-bold text-[#132952] underline underline-offset-4">
                            ( TIM PENILAI SPK )
                        </p>
                        <p class="text-[10px] font-mono text-[#94A3B8]">
                            NIP. Verifikator Lapangan
                        </p>
                    </div>
                </div>

                <div class="space-y-16">
                    <p class="font-mono text-[11px] text-[#64748B]">
                        Mengetahui & Menyetujui,<br>
                        Kepala Dinas Koperasi & UKM Kab. Kutai Timur
                    </p>
                    <div>
                        <p class="font-bold text-[#132952] underline underline-offset-4">
                            ( TEGUH BUDI SANTOSO, S.E., M.Si )
                        </p>
                        <p class="text-[10px] font-mono text-[#94A3B8]">
                            Pembina Utama Muda / NIP. 197405122002121004
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex items-center justify-between w-full">
                <span class="text-[11px] font-mono text-[#64748B]">
                    Format Berita Acara Perbup Kutim
                </span>
                <div class="flex items-center gap-2">
                    <BaseButton variant="outline" size="sm" @click="$emit('close')">
                        Tutup
                    </BaseButton>
                    <BaseButton variant="navy" size="sm" @click="handlePrint">
                        <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Laporan (Print)</span>
                    </BaseButton>
                </div>
            </div>
        </template>
    </BaseModal>
</template>
