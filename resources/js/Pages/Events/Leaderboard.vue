<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import CobaltLeaderboardTable from '@/Components/Public/CobaltLeaderboardTable.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    awarding_tier: {
        type: String,
        default: 'top_10',
    },
    leaderboard: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <PublicLayout>
        <Head :title="`Klasemen Stand UMKM - ${event.nama_event} - PATEN Kutai Timur`" />

        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-8">
            <!-- Navigation Back Bar & Breadcrumb -->
            <div class="flex items-center justify-between">
                <Link
                    :href="`/e/${event.slug}`"
                    class="inline-flex items-center gap-1.5 font-mono text-xs text-[#2563EB] hover:text-[#1D4ED8] transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Portal Acara</span>
                </Link>

                <span class="font-mono text-xs uppercase tracking-wider text-[#64748B]">
                    Modul 1: Publik Awarding
                </span>
            </div>

            <!-- Page Header Section -->
            <div class="border-b border-[#E2E8F0] pb-6">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#2563EB] font-semibold">
                        KLASEMEN & PENGHARGAAN STAND UMKM
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-display font-bold text-[#0F172A] tracking-tight">
                    {{ event.nama_event }}
                </h1>
                <p class="text-sm text-[#475569] mt-2 max-w-2xl leading-relaxed">
                    Penghargaan keaktifan dan peringkat stand dihitung otomatis berdasarkan intensitas pencatatan transaksi kasir digital POS dan konsistensi pelayanan stand selama gelaran bazar.
                </p>

                <div class="flex flex-wrap items-center gap-4 mt-4 text-xs text-[#64748B]">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Penyelenggara: <strong class="text-[#0F172A] ml-0.5">{{ event.penyelenggara }}</strong>
                    </span>
                    <span class="text-[#CBD5E1]">&bull;</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        Venue: <strong class="text-[#0F172A] ml-0.5">{{ event.lokasi_venue }}</strong>
                    </span>
                </div>
            </div>

            <!-- Leaderboard Table Component -->
            <CobaltLeaderboardTable
                :leaderboard="leaderboard"
                :event="event"
                :awarding-tier="awarding_tier"
            />
        </div>
    </PublicLayout>
</template>
