<script setup>
import { computed } from 'vue';

const props = defineProps({
    metrics: {
        type: Object,
        required: true,
    },
});

const formattedOmzet = computed(() => {
    const val = Number(props.metrics?.total_omzet || 0);
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
});

const attendancePercentage = computed(() => {
    const total = Number(props.metrics?.total_attendees || 0);
    const checkedIn = Number(props.metrics?.checked_in_attendees || 0);
    if (total === 0) return 0;
    return Math.round((checkedIn / total) * 100);
});
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- KPI 1: Total Omzet Terekam (GMV) - Emerald Accent -->
        <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="h-1 w-full absolute top-0 left-0 bg-[#00B262]"></div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                        AGREGAT OMZET BAZAR
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] bg-[#E6F7EF] text-[#00B262] text-[10px] font-mono font-bold">
                        GMV
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-[#132952] tnum tracking-tight">
                    {{ formattedOmzet }}
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-[#F1F5F9] flex items-center justify-between text-[11px] font-mono text-[#64748B]">
                <span>Pencatatan POS Mini</span>
                <span class="text-[#00B262] font-semibold">100% Realtime</span>
            </div>
        </div>

        <!-- KPI 2: Total Transaksi Kasir - Navy Accent -->
        <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="h-1 w-full absolute top-0 left-0 bg-[#132952]"></div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                        TRANSAKSI KASIR
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] bg-[#EDF2F9] text-[#132952] text-[10px] font-mono font-semibold">
                        POS
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-[#132952] tnum tracking-tight">
                    {{ (metrics?.total_transaksi || 0).toLocaleString('id-ID') }}
                    <span class="text-sm font-sans font-normal text-[#64748B]">struk</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-[#F1F5F9] flex items-center justify-between text-[11px] font-mono text-[#64748B]">
                <span>Frekuensi Transaksi</span>
                <span class="text-[#132952] font-semibold">Seluruh Stand</span>
            </div>
        </div>

        <!-- KPI 3: Kehadiran Gate - Amber Accent -->
        <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="h-1 w-full absolute top-0 left-0 bg-[#FAA21B]"></div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                        KEHADIRAN DI GATE
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] bg-[#FEF6E8] text-[#FAA21B] text-[10px] font-mono font-bold">
                        {{ attendancePercentage }}% HADIR
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-[#132952] tnum tracking-tight">
                    {{ (metrics?.checked_in_attendees || 0).toLocaleString('id-ID') }}
                    <span class="text-sm font-sans font-normal text-[#64748B]">/ {{ (metrics?.total_attendees || 0).toLocaleString('id-ID') }}</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-[#F1F5F9] flex items-center justify-between text-[11px] font-mono text-[#64748B]">
                <span>Tiket Terverifikasi</span>
                <span class="text-[#FAA21B] font-semibold">{{ (metrics?.total_attendees || 0) - (metrics?.checked_in_attendees || 0) }} Belum Check-in</span>
            </div>
        </div>

        <!-- KPI 4: Stand Aktif - Navy Accent -->
        <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="h-1 w-full absolute top-0 left-0 bg-[#132952]"></div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                        STAND TERDAFTAR
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] bg-[#EDF2F9] text-[#132952] text-[10px] font-mono font-semibold">
                        UMKM
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-[#132952] tnum tracking-tight">
                    {{ (metrics?.total_stands || 0).toLocaleString('id-ID') }}
                    <span class="text-sm font-sans font-normal text-[#64748B]">slot</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-[#F1F5F9] flex items-center justify-between text-[11px] font-mono text-[#64748B]">
                <span>Kapasitas Bazar</span>
                <span class="text-[#00B262] font-semibold">Tersedia PIN Kasir</span>
            </div>
        </div>
    </div>
</template>
