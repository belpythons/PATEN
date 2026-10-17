<script setup>
import { computed } from 'vue';

const props = defineProps({
    program: {
        type: String,
        default: 'fasilitasi_legalitas',
    },
    isPrioritas: {
        type: Boolean,
        default: false,
    },
});

const programConfig = computed(() => {
    switch (props.program) {
        case 'hibah_modal_apbd':
            return {
                title: 'Hibah Modal APBD Kutim (Rp 5 - 25 Juta)',
                code: 'HIBAH APBD',
                badgeClass: 'bg-[#FFFBEB] text-[#92400E] border-[#FAA21B]',
                showCrown: true,
                desc: 'Penerima Terpilih Kuota 200 APBD',
            };
        case 'hibah_alat':
            return {
                title: 'Hibah Peralatan Produksi Pemkab / CSR',
                code: 'HIBAH ALAT',
                badgeClass: 'bg-[#EFF6FF] text-[#1E40AF] border-[#93C5FD]',
                showCrown: false,
                desc: 'Bantuan Alat Usaha & Etalase',
            };
        case 'kredit_perbankan':
            return {
                title: 'Kredit Usaha BPR Kutim / KUR Bankaltimtara',
                code: 'KREDIT BPR / KUR',
                badgeClass: 'bg-[#ECFDF5] text-[#065F46] border-[#00B262]',
                showCrown: false,
                desc: 'Inkubasi & Pembiayaan Bunga Rendah',
            };
        case 'bantuan_modal_awal':
            return {
                title: 'Bantuan Modal Awal BAZNAS & TKM (Rp 5 Juta)',
                code: 'MODAL BAZNAS',
                badgeClass: 'bg-[#F8FAFC] text-[#334155] border-[#CBD5E1]',
                showCrown: false,
                desc: 'Stimulus Modal Usaha Awal',
            };
        case 'fasilitasi_legalitas':
        default:
            return {
                title: 'Fasilitasi Legalitas Usaha (NIB / Halal / P-IRT)',
                code: 'LEGALITAS USAHA',
                badgeClass: 'bg-[#F8FAFC] text-[#132952] border-[#132952]/40',
                showCrown: false,
                desc: 'Pendampingan Legalitas Gratis Dinas',
            };
    }
});
</script>

<template>
    <div class="inline-flex flex-col items-start gap-0.5">
        <span
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[4px] border font-mono text-[11px] font-bold tracking-wider uppercase transition-colors"
            :class="programConfig.badgeClass"
            :title="programConfig.title"
        >
            <span v-if="programConfig.showCrown || isPrioritas" class="text-[#FAA21B]">★</span>
            <span>{{ programConfig.code }}</span>
        </span>
        <span class="text-[10px] font-sans text-[#64748B] pl-0.5 max-w-[200px] truncate" :title="programConfig.desc">
            {{ programConfig.desc }}
        </span>
    </div>
</template>
