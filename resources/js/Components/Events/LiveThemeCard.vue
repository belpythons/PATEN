<script setup>
import { computed } from 'vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    namaEvent: {
        type: String,
        default: 'Pekan Raya Kutai Timur 2026',
    },
    penyelenggara: {
        type: String,
        default: 'Dinas Koperasi & UKM Kab. Kutai Timur',
    },
    lokasiVenue: {
        type: String,
        default: 'Kawasan Bukit Pelangi, Sangatta',
    },
    colorMode: {
        type: String,
        default: 'duo',
    },
    colorPrimary: {
        type: String,
        default: '#2563EB',
    },
    colorSecondary: {
        type: String,
        default: '#F59E0B',
    },
    gamifikasiMode: {
        type: String,
        default: 'doorprize',
    },
    hargaHtm: {
        type: [Number, String],
        default: 0,
    },
});

const gamifikasiLabel = computed(() => {
    switch (props.gamifikasiMode) {
        case 'htm': return `TIKET HTM (RP ${Number(props.hargaHtm).toLocaleString('id-ID')})`;
        case 'doorprize': return 'KUPON DOORPRIZE GRATIS';
        case 'both': return 'HTM & UNDIAN DOORPRIZE';
        default: return 'TANPA TIKET';
    }
});

const cardStyle = computed(() => {
    return {
        '--preview-primary': props.colorPrimary || '#2563EB',
        '--preview-secondary': props.colorMode === 'duo' ? (props.colorSecondary || '#F59E0B') : (props.colorPrimary || '#2563EB'),
    };
});
</script>

<template>
    <div
        :style="cardStyle"
        class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 shadow-xs transition-all relative overflow-hidden"
    >
        <!-- Top color accent stripe -->
        <div
            class="h-1.5 w-full absolute top-0 left-0"
            :style="{ background: colorMode === 'duo' ? `linear-gradient(90deg, ${colorPrimary} 0%, ${colorSecondary} 100%)` : colorPrimary }"
        ></div>

        <!-- Header status -->
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0] pt-1">
            <span class="text-[10px] font-mono uppercase tracking-[0.06em] text-[#64748B]">
                PRATINJAU LANGSUNG KARTU TEMA
            </span>
            <BaseBadge variant="cobalt">
                {{ colorMode.toUpperCase() }} MODE
            </BaseBadge>
        </div>

        <!-- Event simulation card -->
        <div class="rounded-[8px] border border-[#E2E8F0] p-4 bg-[#F8FAFC]">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="h-2.5 w-2.5 rounded-full shrink-0"
                    :style="{ backgroundColor: colorPrimary }"
                ></span>
                <span class="text-[11px] font-mono text-[#64748B] uppercase truncate">
                    {{ penyelenggara || 'Pemerintah Kabupaten Kutai Timur' }}
                </span>
            </div>

            <h4 class="font-display font-bold text-base sm:text-lg text-[#0F172A] leading-snug line-clamp-2">
                {{ namaEvent || 'Nama Event Anda' }}
            </h4>

            <p class="mt-1 text-xs text-[#475569] flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5 text-[#64748B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ lokasiVenue || 'Lokasi Venue' }}</span>
            </p>

            <!-- Mockup Ticket Strip -->
            <div class="mt-4 pt-3 border-t border-dashed border-[#CBD5E1] flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-mono uppercase text-[#64748B] block">AKSES PENGUNJUNG</span>
                    <span
                        class="text-[11px] font-mono font-bold"
                        :style="{ color: colorPrimary }"
                    >
                        {{ gamifikasiLabel }}
                    </span>
                </div>
                <div class="h-7 w-7 rounded-[4px] border border-[#CBD5E1] bg-white flex items-center justify-center p-0.5">
                    <svg class="h-full w-full text-[#0F172A]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm14-2h3v3h-3v-3zm-5 0h3v3h-3v-3zm0 5h3v3h-3v-3zm5 0h3v3h-3v-3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Color swatch indicators -->
        <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center justify-between text-xs font-mono">
            <div class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-[4px] border border-black/10" :style="{ backgroundColor: colorPrimary }"></span>
                <span class="text-[11px] text-[#475569]">{{ colorPrimary }}</span>
            </div>
            <div v-if="colorMode === 'duo'" class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-[4px] border border-black/10" :style="{ backgroundColor: colorSecondary }"></span>
                <span class="text-[11px] text-[#475569]">{{ colorSecondary }}</span>
            </div>
        </div>
    </div>
</template>
