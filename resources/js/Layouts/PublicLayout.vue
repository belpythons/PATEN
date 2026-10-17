<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import GlobalCommandPalette from '@/Components/Navigation/GlobalCommandPalette.vue';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const isPaletteOpen = ref(false);

const openPalette = () => {
    isPaletteOpen.value = true;
};

const closePalette = () => {
    isPaletteOpen.value = false;
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#132952] font-sans antialiased">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xs border-b border-[#E2E8F0]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <!-- Brandmark -->
                <div class="flex items-center gap-6">
                    <BrandMark :show-version="true" />
                    <nav class="hidden md:flex items-center gap-4 text-xs font-mono uppercase tracking-wider text-[#5B6B8A]">
                        <Link href="/" class="hover:text-[#132952] transition-colors">
                            Beranda
                        </Link>
                        <Link href="/#events-registry" class="hover:text-[#00B262] transition-colors flex items-center gap-1">
                            <span>Daftar Stand</span>
                            <span class="text-[10px] px-1 py-0.2 rounded bg-[#00B262]/10 text-[#00B262] border border-[#00B262]/30 font-semibold">UMKM</span>
                        </Link>
                        <Link href="/pos" class="hover:text-[#00B262] transition-colors flex items-center gap-1 font-semibold text-[#132952]">
                            <span>Kasir POS</span>
                            <span class="text-[10px] px-1 py-0.2 rounded bg-[#00B262]/10 text-[#00B262] border border-[#00B262]/30">STAND</span>
                        </Link>
                        <Link href="/events/create" class="hover:text-[#132952] transition-colors">
                            Buat Event
                        </Link>
                    </nav>
                </div>

                <!-- Right affordances: ⌘K trigger + Auth / Action -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        class="hidden sm:inline-flex items-center gap-2 px-2.5 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-[#F8FAFC] text-xs text-[#5B6B8A] hover:border-[#132952] hover:text-[#132952] transition-colors cursor-pointer"
                        @click="openPalette"
                    >
                        <span>Perintah</span>
                        <kbd class="font-mono text-[10px] bg-white border border-[#CBD5E1] px-1 py-0.5 rounded text-[#475569]">
                            ⌘K
                        </kbd>
                    </button>

                    <!-- Authenticated User Links -->
                    <template v-if="authUser">
                        <Link
                            href="/dashboard"
                            class="inline-flex items-center px-3 py-1.5 rounded-[6px] bg-[#132952] text-white text-xs font-medium hover:bg-[#0D1E3D] transition-colors shadow-xs"
                        >
                            <span>Dasbor Saya</span>
                        </Link>
                        <button
                            type="button"
                            @click="logout"
                            class="px-2 py-1.5 rounded-[6px] text-xs font-mono text-[#EF4444] hover:bg-[#EF4444]/10 transition-colors cursor-pointer"
                            title="Keluar akun"
                        >
                            Keluar
                        </button>
                    </template>

                    <!-- Guest Links -->
                    <template v-else>
                        <Link
                            href="/login"
                            class="inline-flex items-center px-3 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-white text-[#132952] text-xs font-mono font-medium hover:border-[#132952] transition-colors"
                        >
                            Masuk
                        </Link>
                        <Link
                            href="/events/create"
                            class="inline-flex items-center px-3 py-1.5 rounded-[6px] bg-[#132952] text-white text-xs font-medium hover:bg-[#0D1E3D] transition-colors shadow-xs"
                        >
                            + Inisialisasi
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main Slot -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Colophon Footer -->
        <footer class="border-t border-[#E2E8F0] bg-white mt-12 py-8 text-xs text-[#5B6B8A]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <BrandMark />
                        <p class="mt-2 text-xs leading-relaxed text-[#273A60]">
                            Pemantauan Transaksi & Efektivitas Niaga. Ekosistem digital akuntabilitas event bazar dan seleksi program bantuan UMKM Kabupaten Kutai Timur.
                        </p>
                    </div>
                    <div>
                        <p class="font-mono uppercase tracking-wider text-[11px] font-semibold text-[#132952] mb-2">
                            Instansi Pembina
                        </p>
                        <p class="text-xs leading-relaxed text-[#273A60]">
                            Dinas Koperasi & UKM Kabupaten Kutai Timur<br>
                            Pemerintah Kabupaten Kutai Timur, Kalimantan Timur
                        </p>
                    </div>
                    <div>
                        <p class="font-mono uppercase tracking-wider text-[11px] font-semibold text-[#132952] mb-2">
                            Kepatuhan Regulasi
                        </p>
                        <p class="text-xs leading-relaxed text-[#273A60]">
                            Berdasarkan Peraturan Bupati (Perbup) Kutai Timur tentang Tata Cara Penyaluran Hibah & Bantuan Modal Pelaku Usaha Mikro.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] font-mono text-[#94A3B8]">
                    <span>&copy; {{ new Date().getFullYear() }} Pemkab Kutai Timur &middot; PATEN Platform v1.0.0</span>
                    <span>Designed with Hallmark Triad System &middot; Laravel + Vue + Supabase</span>
                </div>
            </div>
        </footer>

        <!-- Global Command Palette Modal -->
        <GlobalCommandPalette :is-open="isPaletteOpen" @close="closePalette" />
    </div>
</template>
