<script setup>
import { computed } from 'vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    leaderboard: {
        type: Array,
        default: () => [],
    },
    event: {
        type: Object,
        default: () => ({}),
    },
    awardingTier: {
        type: String,
        default: 'top_10',
    },
});

const topThree = computed(() => {
    return props.leaderboard.slice(0, 3);
});

const remainingList = computed(() => {
    return props.leaderboard.slice(3);
});

const formatPoints = (points) => {
    return new Intl.NumberFormat('id-ID').format(points || 0);
};
</script>

<template>
    <div class="space-y-8">
        <!-- Live Status Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-[#E2E8F0] rounded-[10px] p-4 text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#00B262] animate-pulse"></span>
                <span class="font-mono uppercase tracking-wider text-[#132952] font-semibold">
                    Klasemen Resmi Aktif
                </span>
                <span class="text-[#94A3B8]">&bull;</span>
                <span class="text-[#5B6B8A]">Berdasarkan Konsistensi Transaksi POS</span>
            </div>

            <div class="font-mono text-[11px] text-[#5B6B8A] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#132952]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Proteksi Finansial: Omzet Dirahasiakan</span>
            </div>
        </div>

        <!-- Top 3 Podium Highlights -->
        <div v-if="topThree.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Rank 1: Gold / Amber -->
            <div
                v-if="topThree[0]"
                class="md:order-2 bg-[#FEF6E8] border-2 border-[#FAA21B] rounded-[10px] p-5 shadow-xs relative overflow-hidden"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] bg-[#FEF6E8] text-[#C87D0E] border border-[#FCDDA3] font-mono text-[11px] font-bold">
                        🥇 JUARA #01
                    </span>
                    <span class="font-mono text-xs text-[#C87D0E] font-semibold">
                        {{ topThree[0].nomor_stand }}
                    </span>
                </div>
                <h4 class="font-display font-bold text-lg text-[#132952] truncate">
                    {{ topThree[0].nama_usaha }}
                </h4>
                <div class="mt-4 pt-3 border-t border-[#FCDDA3] flex items-baseline justify-between">
                    <span class="text-xs text-[#C87D0E] font-medium">Poin Keaktifan:</span>
                    <span class="font-mono text-2xl font-bold text-[#FAA21B] tnum">
                        {{ formatPoints(topThree[0].poin_keaktifan) }}
                        <span class="text-xs font-normal text-[#C87D0E]">Poin</span>
                    </span>
                </div>
            </div>

            <!-- Rank 2: Silver / Navy subtle -->
            <div
                v-if="topThree[1]"
                class="md:order-1 bg-[#EDF2F9] border border-[#C2D2E9] rounded-[10px] p-5 shadow-xs relative overflow-hidden"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] bg-[#EDF2F9] text-[#132952] border border-[#C2D2E9] font-mono text-[11px] font-bold">
                        🥈 PERINGKAT #02
                    </span>
                    <span class="font-mono text-xs text-[#5B6B8A] font-semibold">
                        {{ topThree[1].nomor_stand }}
                    </span>
                </div>
                <h4 class="font-display font-bold text-base text-[#132952] truncate">
                    {{ topThree[1].nama_usaha }}
                </h4>
                <div class="mt-4 pt-3 border-t border-[#C2D2E9] flex items-baseline justify-between">
                    <span class="text-xs text-[#5B6B8A] font-medium">Poin Keaktifan:</span>
                    <span class="font-mono text-xl font-bold text-[#132952] tnum">
                        {{ formatPoints(topThree[1].poin_keaktifan) }}
                        <span class="text-xs font-normal text-[#5B6B8A]">Poin</span>
                    </span>
                </div>
            </div>

            <!-- Rank 3: Bronze / Emerald subtle -->
            <div
                v-if="topThree[2]"
                class="md:order-3 bg-[#E6F7EF] border border-[#A2E8C6] rounded-[10px] p-5 shadow-xs relative overflow-hidden"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] bg-[#E6F7EF] text-[#008046] border border-[#A2E8C6] font-mono text-[11px] font-bold">
                        🥉 PERINGKAT #03
                    </span>
                    <span class="font-mono text-xs text-[#008046] font-semibold">
                        {{ topThree[2].nomor_stand }}
                    </span>
                </div>
                <h4 class="font-display font-bold text-base text-[#132952] truncate">
                    {{ topThree[2].nama_usaha }}
                </h4>
                <div class="mt-4 pt-3 border-t border-[#A2E8C6] flex items-baseline justify-between">
                    <span class="text-xs text-[#008046] font-medium">Poin Keaktifan:</span>
                    <span class="font-mono text-xl font-bold text-[#00B262] tnum">
                        {{ formatPoints(topThree[2].poin_keaktifan) }}
                        <span class="text-xs font-normal text-[#008046]">Poin</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Full Precision Table (All Rankings) -->
        <div class="bg-white border border-[#E2E8F0] rounded-[10px] shadow-xs overflow-hidden">
            <div class="px-5 py-3 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
                <span class="font-mono text-xs uppercase tracking-wider text-[#5B6B8A] font-semibold">
                    Daftar Tabulasi Peringkat Keaktifan
                </span>
                <span class="font-mono text-xs text-[#94A3B8]">
                    Total: {{ leaderboard.length }} Stand
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[#5B6B8A] font-mono uppercase tracking-wider">
                            <th class="py-3 px-4 w-20 text-center">Peringkat</th>
                            <th class="py-3 px-4 w-28">No. Stand</th>
                            <th class="py-3 px-4">Nama Usaha UMKM</th>
                            <th class="py-3 px-4 text-right">Poin Keaktifan</th>
                            <th class="py-3 px-4 text-center w-28">Lencana</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr
                            v-for="item in leaderboard"
                            :key="item.peringkat"
                            class="hover:bg-[#F8FAFC] transition-colors"
                            :class="{
                                'bg-[#FEF6E8]/40': item.peringkat === 1,
                                'bg-[#EDF2F9]/50': item.peringkat === 2,
                                'bg-[#E6F7EF]/40': item.peringkat === 3,
                            }"
                        >
                            <td class="py-3.5 px-4 text-center font-mono font-bold" :class="{
                                'text-[#FAA21B]': item.peringkat === 1,
                                'text-[#132952]': item.peringkat === 2,
                                'text-[#00B262]': item.peringkat === 3,
                                'text-[#5B6B8A]': item.peringkat > 3,
                            }">
                                #{{ String(item.peringkat).padStart(2, '0') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-[#132952]">
                                {{ item.nomor_stand }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-[#132952]">
                                    {{ item.nama_usaha }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-sm text-[#132952] tnum">
                                {{ formatPoints(item.poin_keaktifan) }}
                                <span class="text-[10px] font-normal text-[#5B6B8A]">pts</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <BaseBadge
                                    v-if="item.peringkat === 1"
                                    variant="amber"
                                    size="sm"
                                >
                                    Emas
                                </BaseBadge>
                                <BaseBadge
                                    v-else-if="item.peringkat === 2"
                                    variant="navy"
                                    size="sm"
                                >
                                    Perak
                                </BaseBadge>
                                <BaseBadge
                                    v-else-if="item.peringkat === 3"
                                    variant="emerald"
                                    size="sm"
                                >
                                    Perunggu
                                </BaseBadge>
                                <span v-else class="text-[11px] font-mono text-[#94A3B8]">
                                    Stand Aktif
                                </span>
                            </td>
                        </tr>

                        <tr v-if="leaderboard.length === 0">
                            <td colspan="5" class="py-12 text-center text-[#94A3B8]">
                                <div class="font-mono text-xs uppercase tracking-wider mb-1">
                                    Belum Ada Transaksi Tercatat
                                </div>
                                <p class="text-xs text-[#5B6B8A]">
                                    Klasemen keaktifan akan otomatis terisi saat stand mulai melayani transaksi digital di lapangan.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Privacy & Calculation Policy Banner -->
        <div class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-[6px] p-4 text-xs text-[#273A60] leading-relaxed">
            <h5 class="font-mono text-[11px] uppercase tracking-wider text-[#132952] font-semibold mb-1">
                Kepatuhan Privasi Finansial & Seleksi Terukur:
            </h5>
            <p>
                Sesuai dengan standar perlindungan data usaha mikro PATEN Kutai Timur, rincian nominal omzet rupiah peserta dilindungi dan tidak dipublikasikan ke kanal publik. Peringkat dihitung secara objektif menggunakan algoritma SPK berbasis volume pelayanan transaksi kasir, konsistensi pelaporan, dan kelengkapan katalog niaga.
            </p>
        </div>
    </div>
</template>
