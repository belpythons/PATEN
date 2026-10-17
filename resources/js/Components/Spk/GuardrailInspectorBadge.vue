<script setup>
import { computed } from 'vue';

const props = defineProps({
    evaluation: {
        type: Object,
        required: true,
    },
});

const guardrailStatus = computed(() => {
    const isPrioritas = Boolean(props.evaluation.is_prioritas_hibah_200);
    const catatan = props.evaluation.catatan_dinas || '';
    const program = props.evaluation.rekomendasi_program || '';

    if (isPrioritas && program === 'hibah_modal_apbd') {
        return {
            type: 'passed',
            label: '✓ LOLOS PERBUP',
            sublabel: 'Lolos 4 Aturan Mutlak',
            classes: 'bg-[#DCFCE7] text-[#15803D] border-[#86EFAC]',
        };
    }

    if (catatan.toLowerCase().includes('kesamaan no kk') || catatan.toLowerCase().includes('duplikasi')) {
        return {
            type: 'disqualified',
            label: '✗ DISKUALIFIKASI: DUPLIKASI NO KK',
            sublabel: 'Aturan 1 KK Maksimal 1 Penerima',
            classes: 'bg-[#FEF2F2] text-[#B91C1C] border-[#FCA5A5]',
        };
    }

    if (catatan.toLowerCase().includes('pernah menerima hibah')) {
        return {
            type: 'disqualified',
            label: '✗ DISKUALIFIKASI: PERNAH TERIMA HIBAH',
            sublabel: 'Anti-Duplikasi Bantuan Modal',
            classes: 'bg-[#FEF2F2] text-[#B91C1C] border-[#FCA5A5]',
        };
    }

    if (catatan.toLowerCase().includes('nib')) {
        return {
            type: 'warning',
            label: '✗ NON-HIBAH: NIB BELUM LENGKAP',
            sublabel: 'Dialihkan ke Fasilitasi Legalitas',
            classes: 'bg-[#FFFBEB] text-[#B45309] border-[#FDE68A]',
        };
    }

    if (program === 'hibah_alat') {
        return {
            type: 'alternative',
            label: '✓ LOLOS: HIBAH ALAT USAHA',
            sublabel: 'Kategori Bantuan Peralatan Produksi',
            classes: 'bg-[#EFF6FF] text-[#1D4ED8] border-[#BFDBFE]',
        };
    }

    return {
        type: 'redirected',
        label: 'DIALIHKAN: PEMBINAAN USAHA',
        sublabel: 'Diarahkan Penguatan Usaha',
        classes: 'bg-[#F1F5F9] text-[#475569] border-[#CBD5E1]',
    };
});
</script>

<template>
    <div class="inline-flex flex-col items-start gap-0.5">
        <span
            class="inline-flex items-center px-2 py-0.5 rounded-[3px] border font-mono text-[10px] font-bold tracking-wider uppercase transition-colors"
            :class="guardrailStatus.classes"
            :title="evaluation.catatan_dinas || guardrailStatus.sublabel"
        >
            {{ guardrailStatus.label }}
        </span>
        <span class="text-[9px] font-mono text-[#64748B] pl-0.5">
            {{ guardrailStatus.sublabel }}
        </span>
    </div>
</template>
