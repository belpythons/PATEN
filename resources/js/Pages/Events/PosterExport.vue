<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BrandMark from '@/Components/Navigation/BrandMark.vue';

const props = defineProps({
    poster: {
        type: Object,
        required: true,
    },
    event: {
        type: Object,
        default: null,
    },
});

const format = ref('a4'); // 'a4' or 'square'

const printPoster = () => {
    window.print();
};

const posterThemeStyle = computed(() => {
    return {
        '--poster-primary': props.poster.color_primary || '#132952',
        '--poster-secondary': props.poster.color_mode === 'duo' ? (props.poster.color_secondary || '#00B262') : (props.poster.color_primary || '#132952'),
    };
});
</script>

<template>
    <div class="min-h-screen bg-[#F1F5F9] py-6 sm:py-10 font-sans text-[#0F172A] print:p-0 print:bg-white print:min-h-0">
        <!-- Control Toolbar (Hidden in Print) -->
        <div class="max-w-4xl mx-auto px-4 mb-6 print:hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-[10px] border border-[#CBD5E1] shadow-xs">
                <div class="flex items-center gap-3">
                    <BrandMark />
                    <span class="text-[#CBD5E1] font-mono">/</span>
                    <span class="text-xs font-mono uppercase tracking-wider text-[#64748B]">
                        GENERATOR POSTER RESMI
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Format Toggle -->
                    <div class="inline-flex rounded-[6px] border border-[#CBD5E1] p-0.5 bg-[#F8FAFC] text-xs font-mono">
                        <button
                            type="button"
                            class="px-3 py-1 rounded-[4px] transition-colors cursor-pointer"
                            :class="format === 'a4' ? 'bg-[#132952] text-white font-semibold' : 'text-[#64748B] hover:text-[#0F172A]'"
                            @click="format = 'a4'"
                        >
                            A4 Cetak
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1 rounded-[4px] transition-colors cursor-pointer"
                            :class="format === 'square' ? 'bg-[#132952] text-white font-semibold' : 'text-[#64748B] hover:text-[#0F172A]'"
                            @click="format = 'square'"
                        >
                            1:1 Medsos
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <BaseButton variant="primary" size="sm" @click="printPoster">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Poster (PDF)
                    </BaseButton>

                    <a :href="poster.public_url" target="_blank">
                        <BaseButton variant="outline" size="sm">
                            Buka URL &rarr;
                        </BaseButton>
                    </a>
                </div>
            </div>
        </div>

        <!-- Poster Canvas (Triad Deep Navy Blue Ruler-Drawn Geometry) -->
        <div class="max-w-4xl mx-auto px-4 flex justify-center print:p-0 print:max-w-none">
            <div
                :style="posterThemeStyle"
                class="bg-white border-2 border-[#0F172A] shadow-md transition-all relative overflow-hidden flex flex-col justify-between print:border-none print:shadow-none print:w-full print:h-screen"
                :class="format === 'a4' ? 'w-full max-w-[620px] aspect-[1/1.414] p-8 sm:p-12' : 'w-full max-w-[580px] aspect-square p-8 sm:p-10'"
            >
                <!-- Top Color Accent Line -->
                <div
                    class="h-2 w-full absolute top-0 left-0"
                    :style="{ background: poster.color_mode === 'duo' ? `linear-gradient(90deg, ${poster.color_primary} 0%, ${poster.color_secondary} 100%)` : poster.color_primary }"
                ></div>

                <!-- Top Section: Institutional Masthead -->
                <div>
                    <div class="flex items-center justify-between border-b border-[#0F172A] pb-4 mb-6">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold tracking-widest text-[#0F172A] uppercase">
                                PEMERINTAH KABUPATEN KUTAI TIMUR
                            </span>
                        </div>
                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 rounded-[4px] border border-[#0F172A] bg-[#F8FAFC]">
                            PUBLIKASI RESMI
                        </span>
                    </div>

                    <p class="text-xs font-mono uppercase tracking-[0.08em] text-[#64748B] mb-2 font-medium">
                        {{ poster.penyelenggara }}
                    </p>

                    <h1 class="font-display font-bold text-2xl sm:text-4xl text-[#0F172A] tracking-tight leading-tight uppercase">
                        {{ poster.nama_event }}
                    </h1>

                    <p class="mt-3 text-xs sm:text-sm text-[#334155] leading-relaxed line-clamp-3">
                        {{ poster.deskripsi }}
                    </p>
                </div>

                <!-- Middle Section: Venue & Schedule Box -->
                <div class="my-6 py-4 border-y border-[#CBD5E1] grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-mono uppercase text-[#64748B] block">WAKTU PELAKSANAAN</span>
                        <p class="text-xs sm:text-sm font-mono font-bold text-[#0F172A] mt-0.5">
                            {{ poster.tanggal_mulai }}
                        </p>
                        <p class="text-[11px] font-mono text-[#475569]">
                            s/d {{ poster.tanggal_selesai }}
                        </p>
                    </div>

                    <div>
                        <span class="text-[10px] font-mono uppercase text-[#64748B] block">LOKASI VENUE</span>
                        <p class="text-xs sm:text-sm font-semibold text-[#0F172A] mt-0.5 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-[#132952] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ poster.lokasi_venue }}</span>
                        </p>
                    </div>
                </div>

                <!-- Bottom Section: Dynamic QR Code & URL Link -->
                <div class="pt-4 border-t border-[#0F172A] flex items-center justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] block">
                            PINDAI KODE QR UNTUK INFO LENGKAP & KUPON
                        </span>
                        <p class="text-xs font-mono font-bold text-[#132952] truncate">
                            {{ poster.public_url }}
                        </p>
                        <div class="flex items-center gap-2 pt-1">
                            <BaseBadge variant="emerald" :dot="true">
                                {{ poster.gamifikasi_mode.toUpperCase() }}
                            </BaseBadge>
                            <span class="text-[10px] font-mono text-[#64748B]">PORTAL RESMI PATEN</span>
                        </div>
                    </div>

                    <!-- QR Code SVG Box -->
                    <div class="shrink-0 p-2 rounded-[6px] border border-[#0F172A] bg-white flex items-center justify-center">
                        <div v-if="poster.qr_code_svg" class="w-24 h-24 sm:w-28 sm:h-28" v-html="poster.qr_code_svg"></div>
                        <div v-else class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center text-xs font-mono text-[#94A3B8]">
                            [QR Code]
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        margin: 0;
        size: A4 portrait;
    }
    body {
        margin: 0;
        padding: 0;
        background: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
