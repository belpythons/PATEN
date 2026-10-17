<script setup>
import GuardrailInspectorBadge from '@/Components/Spk/GuardrailInspectorBadge.vue';
import ProgramAllocationBadge from '@/Components/Spk/ProgramAllocationBadge.vue';

const props = defineProps({
    evaluations: {
        type: Array,
        default: () => [],
    },
});

const maskKk = (noKk) => {
    if (!noKk || noKk.length < 8) return '-';
    return noKk.substring(0, 6) + '******' + noKk.substring(noKk.length - 4);
};

const getClusterBadge = (cluster) => {
    switch (cluster) {
        case 'Established High Value':
            return 'bg-[#DCFCE7] text-[#15803D] border-[#86EFAC]';
        case 'Micro Fast Moving':
            return 'bg-[#EFF6FF] text-[#1D4ED8] border-[#BFDBFE]';
        case 'Emerging Artisan':
        default:
            return 'bg-[#F1F5F9] text-[#475569] border-[#CBD5E1]';
    }
};
</script>

<template>
    <div class="rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs overflow-hidden">
        <!-- Table Header Info Bar -->
        <div class="p-4 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-[#F8FAFC]">
            <div>
                <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-[#132952]">
                    Matriks Detail Pembobotan 5 Kriteria SAW & Klaster Machine Learning
                </h3>
                <p class="text-[11px] font-mono text-[#64748B] mt-0.5">
                    Bobot Resmi: w₁ Frekuensi (30%) &middot; w₂ Omzet (20%) &middot; w₃ Konsistensi (25%) &middot; w₄ QRIS (10%) &middot; w₅ Legalitas (15%)
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[3px] bg-[#FFFBEB] border border-[#FAA21B]/40 text-[#B45309] text-[10px] font-mono font-bold">
                    <span>★</span>
                    <span>Highlight: Kuota 200 APBD</span>
                </span>
            </div>
        </div>

        <!-- Horizontal Scrollable Matrix Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#F1F5F9] border-b border-[#CBD5E1] text-[#64748B] font-mono text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-3 w-12 text-center">Rank</th>
                        <th class="py-3 px-4 min-w-[200px] sticky left-0 z-10 bg-[#F1F5F9]">Stand & UMKM</th>
                        <th class="py-3 px-3 min-w-[130px]">Kecamatan</th>
                        <th class="py-3 px-3 text-right min-w-[110px]" title="Bobot 30%: Total transaksi di stand">
                            w₁ Freq (30%)
                        </th>
                        <th class="py-3 px-3 text-right min-w-[120px]" title="Bobot 20%: Total omzet rupiah">
                            w₂ Omzet (20%)
                        </th>
                        <th class="py-3 px-3 text-center min-w-[100px]" title="Bobot 25%: Jam transaksi aktif">
                            w₃ Konsistensi (25%)
                        </th>
                        <th class="py-3 px-3 text-center min-w-[90px]" title="Bobot 10%: Rasio transaksi digital QRIS">
                            w₄ QRIS (10%)
                        </th>
                        <th class="py-3 px-3 text-center min-w-[120px]" title="Bobot 15%: NIB, Halal, P-IRT">
                            w₅ Legalitas (15%)
                        </th>
                        <th class="py-3 px-4 text-center min-w-[140px] bg-[#E2E8F0]/70 font-bold text-[#132952]">
                            Skor SAW (0-100)
                        </th>
                        <th class="py-3 px-3 min-w-[130px]">Klaster ML</th>
                        <th class="py-3 px-4 min-w-[190px]">Guardrail Perbup</th>
                        <th class="py-3 px-4 min-w-[200px]">Rekomendasi Program</th>
                        <th class="py-3 px-4 min-w-[220px]">Catatan Regulasi Dinas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    <tr
                        v-for="(ev, idx) in evaluations"
                        :key="ev.id"
                        class="transition-colors hover:bg-[#F8FAFC]"
                        :class="ev.is_prioritas_hibah_200 ? 'bg-[#FFFBEB]/40' : ''"
                    >
                        <!-- Peringkat -->
                        <td class="py-3 px-3 text-center font-mono font-bold" :class="idx < 3 ? 'text-[#00B262]' : 'text-[#64748B]'">
                            <span v-if="idx === 0">🥇 #1</span>
                            <span v-else-if="idx === 1">🥈 #2</span>
                            <span v-else-if="idx === 2">🥉 #3</span>
                            <span v-else>#{{ idx + 1 }}</span>
                        </td>

                        <!-- Stand & UMKM (Sticky First Column) -->
                        <td
                            class="py-3 px-4 sticky left-0 z-10 transition-colors"
                            :class="ev.is_prioritas_hibah_200 ? 'bg-[#FFFBEB]' : 'bg-white'"
                        >
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-[#132952] px-1.5 py-0.5 rounded-[3px] bg-[#132952]/10">
                                    {{ ev.stand ? ev.stand.nomor_stand : '-' }}
                                </span>
                                <span class="font-semibold text-xs text-[#132952] truncate max-w-[140px]" :title="ev.umkm_profile?.nama_usaha">
                                    {{ ev.umkm_profile ? ev.umkm_profile.nama_usaha : 'Stand Terdaftar' }}
                                </span>
                            </div>
                            <div class="text-[11px] font-mono text-[#64748B] mt-0.5 flex items-center gap-1">
                                <span>{{ ev.umkm_profile ? ev.umkm_profile.nama_pemilik : '-' }}</span>
                                <span>&middot;</span>
                                <span class="text-[#94A3B8]" :title="`No KK: ${ev.umkm_profile?.no_kk}`">
                                    KK: {{ maskKk(ev.umkm_profile?.no_kk) }}
                                </span>
                            </div>
                        </td>

                        <!-- Kecamatan & Kategori -->
                        <td class="py-3 px-3 text-xs">
                            <span class="font-medium text-[#132952] block">
                                {{ ev.umkm_profile?.kecamatan || 'Kutim' }}
                            </span>
                            <span class="text-[10px] font-mono uppercase text-[#64748B]">
                                {{ ev.stand?.kategori_jualan || '-' }}
                            </span>
                        </td>

                        <!-- w1: Frekuensi Transaksi -->
                        <td class="py-3 px-3 text-right font-mono">
                            <span class="font-bold text-[#132952]">
                                {{ ev.stand?.total_transaksi_terekam || 0 }}
                            </span>
                            <span class="text-[10px] text-[#94A3B8] block">struk</span>
                        </td>

                        <!-- w2: Total Omzet -->
                        <td class="py-3 px-3 text-right font-mono">
                            <span class="font-bold text-[#132952]">
                                Rp {{ Number(ev.stand?.total_omzet_terekam || 0).toLocaleString('id-ID') }}
                            </span>
                        </td>

                        <!-- w3: Konsistensi Jam Aktif -->
                        <td class="py-3 px-3 text-center font-mono">
                            <span class="font-semibold text-[#132952]">
                                {{ (Number(ev.score_konsistensi_transaksi || 0) * 100).toFixed(0) }}%
                            </span>
                            <div class="w-12 mx-auto bg-[#E2E8F0] h-1.5 rounded-full overflow-hidden mt-1">
                                <div
                                    class="bg-[#132952] h-full rounded-full"
                                    :style="{ width: `${Math.min(100, Number(ev.score_konsistensi_transaksi || 0) * 100)}%` }"
                                ></div>
                            </div>
                        </td>

                        <!-- w4: Rasio QRIS -->
                        <td class="py-3 px-3 text-center font-mono">
                            <span class="font-semibold text-[#00B262]">
                                {{ (Number(ev.score_digitalisasi_qris || 0) * 100).toFixed(0) }}%
                            </span>
                        </td>

                        <!-- w5: Legalitas (NIB, Halal, PIRT) -->
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <span
                                    class="px-1 py-0.5 rounded-[2px] font-mono text-[9px] font-bold"
                                    :class="ev.umkm_profile?.nib ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#F1F5F9] text-[#94A3B8]'"
                                    title="NIB"
                                >
                                    NIB
                                </span>
                                <span
                                    class="px-1 py-0.5 rounded-[2px] font-mono text-[9px] font-bold"
                                    :class="ev.umkm_profile?.has_halal ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#F1F5F9] text-[#94A3B8]'"
                                    title="Sertifikat Halal"
                                >
                                    HALAL
                                </span>
                                <span
                                    class="px-1 py-0.5 rounded-[2px] font-mono text-[9px] font-bold"
                                    :class="ev.umkm_profile?.has_pirt ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#F1F5F9] text-[#94A3B8]'"
                                    title="Izin P-IRT"
                                >
                                    P-IRT
                                </span>
                            </div>
                        </td>

                        <!-- Skor Akhir SAW (Highlight Column) -->
                        <td class="py-3 px-4 text-center bg-[#E2E8F0]/30 font-mono">
                            <span class="text-base font-bold text-[#132952] block tabular-nums">
                                {{ ev.skor_akhir_spk }}
                            </span>
                            <div class="w-16 mx-auto bg-[#CBD5E1] h-1.5 rounded-full overflow-hidden mt-0.5">
                                <div
                                    class="h-full rounded-full transition-all duration-300"
                                    :class="ev.skor_akhir_spk >= 60 ? 'bg-[#00B262]' : (ev.skor_akhir_spk >= 40 ? 'bg-[#FAA21B]' : 'bg-[#64748B]')"
                                    :style="{ width: `${Math.min(100, ev.skor_akhir_spk)}%` }"
                                ></div>
                            </div>
                        </td>

                        <!-- Klaster Kategori ML -->
                        <td class="py-3 px-3">
                            <span
                                class="inline-block px-2 py-0.5 rounded-[3px] border font-mono text-[10px] font-bold tracking-wider"
                                :class="getClusterBadge(ev.cluster_kategori)"
                            >
                                {{ ev.cluster_kategori || 'Unclustered' }}
                            </span>
                        </td>

                        <!-- Guardrail Inspector Badge -->
                        <td class="py-3 px-4">
                            <GuardrailInspectorBadge :evaluation="ev" />
                        </td>

                        <!-- Program Allocation Badge -->
                        <td class="py-3 px-4">
                            <ProgramAllocationBadge
                                :program="ev.rekomendasi_program"
                                :is-prioritas="Boolean(ev.is_prioritas_hibah_200)"
                            />
                        </td>

                        <!-- Catatan Regulasi Dinas -->
                        <td class="py-3 px-4 text-[11px] font-sans text-[#5B6B8A] leading-relaxed">
                            {{ ev.catatan_dinas || '-' }}
                        </td>
                    </tr>

                    <tr v-if="!evaluations || evaluations.length === 0">
                        <td colspan="13" class="py-10 text-center text-xs font-mono text-[#94A3B8]">
                            Belum ada data evaluasi. Silakan klik tombol "Jalankan Evaluasi SPK" di panel atas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
