<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import GlobalCommandPalette from '@/Components/Navigation/GlobalCommandPalette.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseToast from '@/Components/UI/BaseToast.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'dashboard',
    },
});

const isPaletteOpen = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const openPalette = () => {
    isPaletteOpen.value = true;
};

const closePalette = () => {
    isPaletteOpen.value = false;
};

const copySecretUrl = () => {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        toastType.value = 'success';
        toastMessage.value = 'Link CMS Rahasia berhasil disalin ke clipboard!';
    }).catch(() => {
        toastType.value = 'error';
        toastMessage.value = 'Gagal menyalin link otomatis.';
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#0F172A] font-sans antialiased">
        <!-- Toast container -->
        <div v-if="toastMessage" class="fixed top-4 right-4 z-50 max-w-sm">
            <BaseToast :message="toastMessage" :type="toastType" @dismiss="toastMessage = ''" />
        </div>

        <!-- Secret Token Alert Bar -->
        <div class="bg-[#0B0F19] text-white border-b border-[#26334D] px-4 py-2 text-xs">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span class="font-mono text-[11px] text-[#94A3B8]">
                        ENCRYPTED SESSION // TOKEN:
                    </span>
                    <code class="font-mono text-[#F8FAFC] text-[11px] tracking-wider">
                        {{ token.substring(0, 12) }}...{{ token.substring(token.length - 8) }}
                    </code>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 px-2 py-1 rounded-[4px] bg-[#1E293B] hover:bg-[#2563EB] text-[#E2E8F0] hover:text-white text-[11px] font-mono transition-colors border border-[#334155] cursor-pointer"
                        @click="copySecretUrl"
                    >
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Salin Link CMS</span>
                    </button>
                    <a
                        :href="`/e/${event.slug}`"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-[4px] bg-[#1E293B] hover:bg-[#334155] text-[#94A3B8] hover:text-white text-[11px] font-mono transition-colors border border-[#334155]"
                    >
                        <span>Buka Publik</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main CMS Navbar -->
        <header class="bg-white border-b border-[#E2E8F0] sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="h-14 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <BrandMark :href="`/cms/${event.slug}?token=${token}`" />
                        <span class="text-[#CBD5E1] font-mono">/</span>
                        <div class="flex items-center gap-2">
                            <h1 class="font-display font-semibold text-sm text-[#0F172A] truncate max-w-xs sm:max-w-md">
                                {{ event.nama_event }}
                            </h1>
                            <BaseBadge variant="cobalt" :dot="true">
                                CMS Admin
                            </BaseBadge>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-[#F8FAFC] text-xs text-[#64748B] hover:border-[#2563EB] hover:text-[#0F172A] transition-colors cursor-pointer"
                            @click="openPalette"
                        >
                            <span class="hidden sm:inline">Perintah</span>
                            <kbd class="font-mono text-[10px] bg-white border border-[#CBD5E1] px-1 py-0.5 rounded text-[#475569]">
                                ⌘K
                            </kbd>
                        </button>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <nav class="flex space-x-6 -mb-px text-xs font-mono uppercase tracking-wider overflow-x-auto">
                    <Link
                        :href="`/cms/${event.slug}?token=${token}`"
                        class="py-3 border-b-2 font-medium transition-colors whitespace-nowrap"
                        :class="activeTab === 'dashboard' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#64748B] hover:text-[#0F172A] hover:border-[#CBD5E1]'"
                    >
                        01 / Dashboard Utama
                    </Link>
                    <Link
                        :href="`/cms/${event.slug}/evaluations?token=${token}`"
                        class="py-3 border-b-2 font-medium transition-colors whitespace-nowrap"
                        :class="activeTab === 'evaluations' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#64748B] hover:text-[#0F172A] hover:border-[#CBD5E1]'"
                    >
                        02 / Evaluasi SPK & ML
                    </Link>
                    <a
                        :href="`/e/${event.slug}/leaderboard`"
                        target="_blank"
                        class="py-3 border-b-2 border-transparent text-[#64748B] hover:text-[#0F172A] font-medium transition-colors whitespace-nowrap"
                    >
                        03 / Klasemen Publik ↗
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-6 sm:py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-[#E2E8F0] bg-white py-6 text-xs text-[#64748B]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 font-mono text-[11px]">
                <span>Penyelenggara: {{ event.penyelenggara }} &middot; Lokasi: {{ event.lokasi_venue }}</span>
                <span>Protected by VerifyCmsTokenMiddleware &middot; PATEN Kutim</span>
            </div>
        </footer>

        <!-- Global Command Palette -->
        <GlobalCommandPalette :is-open="isPaletteOpen" @close="closePalette" />
    </div>
</template>
