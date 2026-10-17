<script setup>
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_evaluated: 0,
            prioritas_hibah_200: 0,
            fasilitasi_legalitas: 0,
            hibah_alat: 0,
            kredit_perbankan: 0,
        }),
    },
    isEvaluating: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['runEvaluation', 'openReport']);
</script>

<template>
    <div class="space-y-4">
        <!-- Workbench Top Header -->
        <div class="p-5 sm:p-6 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#132952] bg-[#132952]/10 px-2 py-0.5 rounded-[3px]">
                            04 / SISTEM PENDUKUNG KEPUTUSAN & PIPELINE ML KUTIM
                        </span>
                        <BaseBadge variant="emerald" :dot="true">
                            SAW Algoritma
                        </BaseBadge>
                    </div>

                    <h1 class="mt-2 text-xl sm:text-2xl font-bold font-display text-[#132952] tracking-tight">
                        Matriks Evaluasi Performa Niaga & Rekomendasi Program Bantuan
                    </h1>
                    <p class="mt-1 text-xs text-[#5B6B8A] font-sans leading-relaxed max-w-2xl">
                        Penetapan calon penerima hibah modal APBD, bantuan alat usaha, dan kredit perbankan berbasis pemodelan SAW 5 Kriteria dan verifikasi ketat Guardrail Perbup Kutai Timur (Anti-Duplikasi No KK & Status Hibah).
                    </p>
                </div>

                <!-- Action Affordances -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-[6px] border border-[#CBD5E1] bg-white hover:bg-[#F1F5F9] text-xs font-mono font-semibold text-[#132952] transition-colors cursor-pointer shadow-xs"
                        @click="$emit('openReport')"
                    >
                        <svg class="h-4 w-4 text-[#5B6B8A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Cetak Berita Acara Rekap</span>
                    </button>

                    <button
                        type="button"
                        :disabled="isEvaluating"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-[6px] bg-[#132952] hover:bg-[#0C1830] active:scale-95 text-white text-xs font-mono font-semibold transition-all cursor-pointer shadow-sm disabled:opacity-50"
                        @click="$emit('runEvaluation')"
                    >
                        <svg
                            v-if="isEvaluating"
                            class="animate-spin h-3.5 w-3.5 text-[#00B262]"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <svg v-else class="h-3.5 w-3.5 text-[#00B262]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ isEvaluating ? 'Menghitung SAW & Guardrail...' : 'Jalankan Evaluasi SPK' }}</span>
                    </button>
                </div>
            </div>

            <!-- 5 Instrument KPI Metrik Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-5 pt-4 border-t border-[#E2E8F0]">
                <!-- Metric 1: Total Stand Dinilai -->
                <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1]">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] block truncate">
                        Stand Terdaftar
                    </span>
                    <p class="mt-1 text-xl font-bold font-mono text-[#132952] tabular-nums">
                        {{ summary.total_evaluated || 0 }}
                    </p>
                    <span class="text-[10px] font-mono text-[#94A3B8]">
                        Data Transaksi Lengkap
                    </span>
                </div>

                <!-- Metric 2: Kuota Prioritas Hibah 200 APBD (Highlight Gold/Amber) -->
                <div class="p-3 rounded-[8px] bg-[#FFFBEB] border border-[#FAA21B]/50 shadow-xs relative overflow-hidden">
                    <div class="absolute right-1 top-1 text-[#FAA21B]/30">
                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#B45309] font-bold block truncate">
                        ★ Kuota 200 APBD
                    </span>
                    <p class="mt-1 text-xl font-bold font-mono text-[#B45309] tabular-nums">
                        {{ summary.prioritas_hibah_200 || 0 }}
                    </p>
                    <span class="text-[10px] font-mono text-[#D97706]">
                        Hibah Modal Prioritas
                    </span>
                </div>

                <!-- Metric 3: Fasilitasi Legalitas -->
                <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1]">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] block truncate">
                        Fasilitasi Legalitas
                    </span>
                    <p class="mt-1 text-xl font-bold font-mono text-[#132952] tabular-nums">
                        {{ summary.fasilitasi_legalitas || 0 }}
                    </p>
                    <span class="text-[10px] font-mono text-[#94A3B8]">
                        NIB / Halal / P-IRT
                    </span>
                </div>

                <!-- Metric 4: Hibah Alat Produksi -->
                <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1]">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] block truncate">
                        Hibah Alat Usaha
                    </span>
                    <p class="mt-1 text-xl font-bold font-mono text-[#132952] tabular-nums">
                        {{ summary.hibah_alat || 0 }}
                    </p>
                    <span class="text-[10px] font-mono text-[#94A3B8]">
                        Alat Penunjang Produksi
                    </span>
                </div>

                <!-- Metric 5: Kredit Perbankan -->
                <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1]">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] block truncate">
                        Kredit BPR / KUR
                    </span>
                    <p class="mt-1 text-xl font-bold font-mono text-[#00B262] tabular-nums">
                        {{ summary.kredit_perbankan || 0 }}
                    </p>
                    <span class="text-[10px] font-mono text-[#94A3B8]">
                        Inkubasi & Pembiayaan
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
