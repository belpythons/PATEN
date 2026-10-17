<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseCard from '@/Components/UI/BaseCard.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    events: {
        type: Array,
        default: () => [],
    },
    metrics: {
        type: Object,
        default: () => ({
            total_events: 0,
            total_stands: 0,
            total_attendees: 0,
            total_omzet: 0,
            total_transactions: 0,
        }),
    },
});

const formatCurrency = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
};

const formatDate = (val) => {
    if (!val) return '-';
    return new Date(val).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#0F172A] font-sans antialiased selection:bg-[#00B262] selection:text-white">
        <Head title="Dasbor Pengelola — PATEN Kutai Timur" />

        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 bg-[#132952] border-b border-[#1E3A6E] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <BrandMark :show-version="false" class="text-white" />
                    <span class="hidden md:inline-block text-[#5B6B8A] font-mono">/</span>
                    <span class="hidden md:inline-block text-xs font-mono tracking-wider uppercase text-[#CBD5E1]">
                        KONSOL PENGELOLA
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-white leading-tight">{{ user.name }}</span>
                        <span class="text-[10px] font-mono text-[#00B262] uppercase">
                            {{ user.role === 'admin_dinas' ? 'Supervisi Dinas Koperasi UKM' : 'Panitia Pelaksana' }}
                        </span>
                    </div>

                    <BaseBadge variant="emerald" :dot="true">
                        {{ user.role === 'admin_dinas' ? 'Dinas' : 'Panitia' }}
                    </BaseBadge>

                    <button
                        type="button"
                        @click="logout"
                        class="px-2.5 py-1.5 rounded-[4px] bg-[#0C1830] hover:bg-[#1E3A6E] text-[#EF4444] hover:text-white border border-[#1E3A6E] text-xs font-mono transition-colors cursor-pointer"
                        title="Keluar dari akun"
                    >
                        Keluar
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Welcome Header Banner -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-[10px] bg-gradient-to-r from-[#132952] to-[#1E3A6E] text-white shadow-md">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-mono text-[#00B262]">
                        <span class="w-2 h-2 rounded-full bg-[#00B262] animate-pulse"></span>
                        <span>PANEL OPERASIONAL TERPUSAT KUTIM</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">
                        Selamat Datang, {{ user.name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-[#CBD5E1] max-w-2xl">
                        {{ user.role === 'admin_dinas'
                            ? 'Memantau seluruh perputaran omzet bazar, alokasi stand, dan hasil kurasi SPK bantuan modal APBD di seluruh kecamatan Kutai Timur.'
                            : 'Kelola rangkaian acara, pembagian stand UMKM, kupon doorprize digital, dan rekapitulasi transaksi bazar Anda.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <Link
                        href="/events/create"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[6px] bg-[#00B262] hover:bg-[#009954] text-white text-xs sm:text-sm font-semibold shadow-sm transition-colors"
                    >
                        <span>+ Buat Event Baru</span>
                    </Link>
                    <Link
                        href="/pos"
                        class="inline-flex items-center gap-2 px-3 py-2.5 rounded-[6px] bg-[#0C1830] hover:bg-[#132952] text-[#FAA21B] border border-[#1E3A6E] text-xs sm:text-sm font-mono transition-colors"
                    >
                        <span>Gerbang POS 📱</span>
                    </Link>
                </div>
            </div>

            <!-- KPI Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-[8px] bg-white border border-[#CBD5E1] shadow-xs space-y-1">
                    <p class="text-[11px] font-mono uppercase text-[#64748B]">Total Event Terdaftar</p>
                    <p class="text-2xl font-bold font-mono text-[#132952]">{{ metrics.total_events }}</p>
                    <p class="text-[10px] text-[#00B262] font-mono">Aktif di Sistem PATEN</p>
                </div>
                <div class="p-4 rounded-[8px] bg-white border border-[#CBD5E1] shadow-xs space-y-1">
                    <p class="text-[11px] font-mono uppercase text-[#64748B]">Stand UMKM Lapangan</p>
                    <p class="text-2xl font-bold font-mono text-[#132952]">{{ metrics.total_stands }}</p>
                    <p class="text-[10px] text-[#5B6B8A] font-mono">Bilik terdistribusi</p>
                </div>
                <div class="p-4 rounded-[8px] bg-white border border-[#CBD5E1] shadow-xs space-y-1">
                    <p class="text-[11px] font-mono uppercase text-[#64748B]">Tiket / Doorprize Warga</p>
                    <p class="text-2xl font-bold font-mono text-[#132952]">{{ metrics.total_attendees }}</p>
                    <p class="text-[10px] text-[#5B6B8A] font-mono">Pengunjung terverifikasi</p>
                </div>
                <div class="p-4 rounded-[8px] bg-white border border-[#CBD5E1] shadow-xs space-y-1">
                    <p class="text-[11px] font-mono uppercase text-[#64748B]">Akumulasi Omzet POS</p>
                    <p class="text-xl sm:text-2xl font-bold font-mono text-[#00B262]">{{ formatCurrency(metrics.total_omzet) }}</p>
                    <p class="text-[10px] text-[#64748B] font-mono">{{ metrics.total_transactions }} transaksi tercatat</p>
                </div>
            </div>

            <!-- Event Directory Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold font-display text-[#132952]">
                            {{ user.role === 'admin_dinas' ? 'Seluruh Event Bazar Kabupaten' : 'Daftar Event yang Anda Kelola' }}
                        </h2>
                        <p class="text-xs text-[#64748B]">
                            Klik salah satu tombol aksi untuk mengelola CMS atau membuka portal publik
                        </p>
                    </div>
                    <span class="text-xs font-mono text-[#64748B]">
                        {{ events.length }} Event
                    </span>
                </div>

                <!-- Event Cards Grid -->
                <div v-if="events && events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="ev in events"
                        :key="ev.id"
                        class="p-5 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs flex flex-col justify-between hover:border-[#132952] transition-colors"
                    >
                        <div class="space-y-3">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between gap-2">
                                <BaseBadge variant="navy" :dot="true">
                                    {{ ev.penyelenggara }}
                                </BaseBadge>
                                <span class="text-[11px] font-mono text-[#64748B]">
                                    {{ formatDate(ev.tanggal_mulai) }}
                                </span>
                            </div>

                            <!-- Event Title -->
                            <div>
                                <h3 class="font-bold text-base text-[#132952] font-display hover:text-[#00B262] transition-colors">
                                    <Link :href="ev.secure_management_token ? `/cms/${ev.slug}?token=${ev.secure_management_token}` : `/cms/${ev.slug}`">
                                        {{ ev.nama_event }}
                                    </Link>
                                </h3>
                                <p class="text-xs text-[#64748B] mt-0.5 flex items-center gap-1">
                                    <span>📍</span>
                                    <span class="truncate">{{ ev.lokasi_venue }}</span>
                                </p>
                            </div>

                            <!-- Mini Stat Badges -->
                            <div class="grid grid-cols-3 gap-1.5 py-2 px-2.5 rounded-[6px] bg-[#F8FAFC] border border-[#E2E8F0] text-center font-mono">
                                <div>
                                    <span class="block text-[10px] text-[#64748B]">STAND</span>
                                    <span class="text-xs font-bold text-[#132952]">{{ ev.stands_count }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-[#64748B]">WARGA</span>
                                    <span class="text-xs font-bold text-[#132952]">{{ ev.attendees_count }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-[#64748B]">OMZET</span>
                                    <span class="text-xs font-bold text-[#00B262]">
                                        {{ formatCurrency(ev.stands?.reduce((acc, s) => acc + Number(s.total_omzet_terekam || 0), 0)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-[#E2E8F0] space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <Link
                                    :href="ev.secure_management_token ? `/cms/${ev.slug}?token=${ev.secure_management_token}` : `/cms/${ev.slug}`"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-[6px] bg-[#132952] hover:bg-[#0D1E3D] text-white text-xs font-mono font-medium transition-colors text-center"
                                >
                                    Kelola CMS ⚙️
                                </Link>
                                <Link
                                    :href="`/e/${ev.slug}`"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-[6px] bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#132952] text-xs font-mono font-medium border border-[#CBD5E1] transition-colors text-center"
                                >
                                    Portal Publik ↗
                                </Link>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <Link
                                    :href="`/e/${ev.slug}/leaderboard`"
                                    class="inline-flex items-center justify-center px-2 py-1.5 rounded-[4px] bg-white hover:bg-[#F8FAFC] text-[#5B6B8A] hover:text-[#132952] text-[11px] font-mono border border-[#E2E8F0] transition-colors text-center"
                                >
                                    Leaderboard 🏆
                                </Link>
                                <Link
                                    :href="ev.secure_management_token ? `/cms/${ev.slug}/evaluations?token=${ev.secure_management_token}` : `/cms/${ev.slug}/evaluations`"
                                    class="inline-flex items-center justify-center px-2 py-1.5 rounded-[4px] bg-white hover:bg-[#F8FAFC] text-[#5B6B8A] hover:text-[#132952] text-[11px] font-mono border border-[#E2E8F0] transition-colors text-center"
                                >
                                    Hasil SPK 📊
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="p-10 rounded-[10px] bg-white border border-[#CBD5E1] text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-[#132952]/10 flex items-center justify-center text-xl">
                        🎪
                    </div>
                    <h3 class="text-base font-bold text-[#132952]">Belum Ada Event Bazar</h3>
                    <p class="text-xs text-[#64748B] max-w-sm mx-auto">
                        Anda belum memiliki event yang aktif. Klik tombol di bawah untuk membuat event bazar pertama Anda.
                    </p>
                    <div class="pt-2">
                        <Link
                            href="/events/create"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-[6px] bg-[#00B262] hover:bg-[#009954] text-white text-xs font-semibold shadow-xs transition-colors"
                        >
                            + Buat Event Sekarang
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="p-4 border-t border-[#E2E8F0] bg-white text-center text-xs font-mono text-[#64748B]">
            Sistem PATEN &middot; Pemerintah Kabupaten Kutai Timur
        </footer>
    </div>
</template>
