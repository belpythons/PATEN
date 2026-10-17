<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import GlobalCommandPalette from '@/Components/Navigation/GlobalCommandPalette.vue';

const isPaletteOpen = ref(false);

const openPalette = () => {
    isPaletteOpen.value = true;
};

const closePalette = () => {
    isPaletteOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#0F172A] font-sans antialiased">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xs border-b border-[#E2E8F0]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <!-- Brandmark -->
                <div class="flex items-center gap-6">
                    <BrandMark :show-version="true" />
                    <nav class="hidden md:flex items-center gap-4 text-xs font-mono uppercase tracking-wider text-[#64748B]">
                        <Link href="/" class="hover:text-[#2563EB] transition-colors">
                            Beranda
                        </Link>
                        <Link href="/events/create" class="hover:text-[#2563EB] transition-colors">
                            Buat Event
                        </Link>
                    </nav>
                </div>

                <!-- Right affordances: ⌘K trigger + Quick action -->
                <div class="flex items-center gap-2 sm:gap-3">
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
                    <Link
                        href="/events/create"
                        class="inline-flex items-center px-3 py-1.5 rounded-[6px] bg-[#2563EB] text-white text-xs font-medium hover:bg-[#1D4ED8] transition-colors shadow-xs"
                    >
                        + Inisialisasi Event
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Slot -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Colophon Footer (Hallmark Cobalt Register) -->
        <footer class="border-t border-[#E2E8F0] bg-white mt-12 py-8 text-xs text-[#64748B]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <BrandMark />
                        <p class="mt-2 text-xs leading-relaxed text-[#475569]">
                            Pemantauan Transaksi & Efektivitas Niaga. Ekosistem digital akuntabilitas event bazar dan seleksi program bantuan UMKM Kabupaten Kutai Timur.
                        </p>
                    </div>
                    <div>
                        <p class="font-mono uppercase tracking-wider text-[11px] font-semibold text-[#0F172A] mb-2">
                            Instansi Pembina
                        </p>
                        <p class="text-xs leading-relaxed text-[#475569]">
                            Dinas Koperasi & UKM Kabupaten Kutai Timur<br>
                            Pemerintah Kabupaten Kutai Timur, Kalimantan Timur
                        </p>
                    </div>
                    <div>
                        <p class="font-mono uppercase tracking-wider text-[11px] font-semibold text-[#0F172A] mb-2">
                            Kepatuhan Regulasi
                        </p>
                        <p class="text-xs leading-relaxed text-[#475569]">
                            Berdasarkan Peraturan Bupati (Perbup) Kutai Timur tentang Tata Cara Penyaluran Hibah & Bantuan Modal Pelaku Usaha Mikro.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] font-mono text-[#94A3B8]">
                    <span>&copy; {{ new Date().getFullYear() }} Pemkab Kutai Timur &middot; PATEN Platform v1.0.0</span>
                    <span>Designed with Hallmark Cobalt &middot; Laravel + Vue + Supabase</span>
                </div>
            </div>
        </footer>

        <!-- Global Command Palette Modal -->
        <GlobalCommandPalette :is-open="isPaletteOpen" @close="closePalette" />
    </div>
</template>
