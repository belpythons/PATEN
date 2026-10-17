<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    qrCodeUrl: {
        type: String,
        default: '',
    },
});

const now = ref(new Date());
let timer = null;

onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const startDate = computed(() => new Date(props.event.tanggal_mulai));
const endDate = computed(() => new Date(props.event.tanggal_selesai));

const eventStatus = computed(() => {
    if (now.value < startDate.value) {
        return { label: 'Menuju Pembukaan', phase: 'upcoming', color: 'navy' };
    } else if (now.value >= startDate.value && now.value <= endDate.value) {
        return { label: 'Sedang Berlangsung', phase: 'live', color: 'emerald' };
    } else {
        return { label: 'Acara Selesai', phase: 'ended', color: 'graphite' };
    }
});

const countdown = computed(() => {
    const target = eventStatus.value.phase === 'upcoming' ? startDate.value : endDate.value;
    const diff = Math.max(0, target.getTime() - now.value.getTime());

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / 1000 / 60) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    return {
        days: String(days).padStart(2, '0'),
        hours: String(hours).padStart(2, '0'),
        minutes: String(minutes).padStart(2, '0'),
        seconds: String(seconds).padStart(2, '0'),
    };
});

const formatDateRange = (start, end) => {
    if (!start) return '-';
    const s = new Date(start);
    const e = end ? new Date(end) : null;

    const opt = { day: 'numeric', month: 'short', year: 'numeric' };
    const startStr = s.toLocaleDateString('id-ID', opt);

    if (e && s.toDateString() !== e.toDateString()) {
        const endStr = e.toLocaleDateString('id-ID', opt);
        return `${startStr} – ${endStr}`;
    }

    return startStr;
};

const gamifikasiLabel = computed(() => {
    switch (props.event.gamifikasi_mode) {
        case 'doorprize': return 'Kupon Doorprize Digital';
        case 'htm': return 'Tiket Masuk (HTM)';
        case 'both': return 'HTM + Doorprize';
        default: return 'Akses Terbuka';
    }
});
</script>

<template>
    <section class="py-8 sm:py-12 border-b border-[#E2E8F0]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <!-- Left Column: Title, Lede, Actions -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Eyebrow -->
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#132952]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                        01 / PORTAL RESMI ACARA
                    </span>
                    <span class="text-xs text-[#94A3B8]">&bull;</span>
                    <span class="font-mono text-xs text-[#5B6B8A]">KUTAI TIMUR</span>
                </div>

                <!-- Event Title -->
                <div>
                    <h1 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl text-[#132952] tracking-tight leading-tight">
                        {{ event.nama_event }}
                    </h1>
                    <p class="mt-3 text-base sm:text-lg text-[#273A60] leading-relaxed">
                        Diselenggarakan oleh <strong class="text-[#132952]">{{ event.penyelenggara }}</strong> dalam rangka penguatan ekosistem niaga dan pemberdayaan pelaku UMKM lokal Kutai Timur.
                    </p>
                </div>

                <!-- Meta Specs Box -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="bg-white border border-[#E2E8F0] rounded-[6px] p-3 text-xs">
                        <span class="text-[#5B6B8A] block font-mono uppercase text-[10px] tracking-wider mb-0.5">
                            Lokasi / Venue
                        </span>
                        <div class="font-medium text-[#132952] flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#132952] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ event.lokasi_venue }}</span>
                        </div>
                    </div>

                    <div class="bg-white border border-[#E2E8F0] rounded-[6px] p-3 text-xs">
                        <span class="text-[#5B6B8A] block font-mono uppercase text-[10px] tracking-wider mb-0.5">
                            Jadwal Pelaksanaan
                        </span>
                        <div class="font-medium text-[#132952] flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#132952] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ formatDateRange(event.tanggal_mulai, event.tanggal_selesai) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Event Description if available -->
                <p v-if="event.deskripsi" class="text-xs sm:text-sm text-[#273A60] leading-relaxed border-l-2 border-[#CBD5E1] pl-3 py-0.5">
                    {{ event.deskripsi }}
                </p>

                <!-- Action Button Group -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a
                        href="#registrasi-tiket"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-[6px] bg-[#132952] text-white text-xs sm:text-sm font-medium hover:bg-[#0D1E3D] transition-colors shadow-xs"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Daftar Kehadiran & Ambil Tiket
                    </a>

                    <a
                        href="#jadwal-rundown"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-[6px] bg-white border border-[#CBD5E1] text-[#132952] text-xs sm:text-sm font-medium hover:bg-[#F8FAFC] transition-colors"
                    >
                        Lihat Rundown
                    </a>

                    <Link
                        :href="`/e/${event.slug}/leaderboard`"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-[6px] bg-white border border-[#CBD5E1] text-[#132952] text-xs sm:text-sm font-medium hover:border-[#132952] hover:text-[#132952] transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-[#FAA21B]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                        </svg>
                        Klasemen Stand
                    </Link>
                </div>
            </div>

            <!-- Right Column: Instrument Status Card (Graphite) -->
            <div class="lg:col-span-5 bg-[#0C1830] text-white p-6 rounded-[10px] border border-[#1E3A6E] shadow-xl space-y-6">
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 border-b border-[#1E3A6E]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#00B262] animate-pulse"></span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-[#94A3B8]">
                            INSTRUMEN PEMANTAUAN
                        </span>
                    </div>
                    <BaseBadge :variant="eventStatus.color" size="sm">
                        {{ eventStatus.label }}
                    </BaseBadge>
                </div>

                <!-- Live Countdown Timer -->
                <div>
                    <div class="text-[11px] font-mono text-[#94A3B8] uppercase tracking-wider mb-2">
                        <template v-if="eventStatus.phase === 'upcoming'">
                            Hitung Mundur Pembukaan Acara
                        </template>
                        <template v-else-if="eventStatus.phase === 'live'">
                            Waktu Tersisa Pelaksanaan
                        </template>
                        <template v-else>
                            Acara Resmi Berakhir
                        </template>
                    </div>

                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="bg-[#132952] border border-[#1E3A6E] rounded-[6px] p-2">
                            <span class="font-mono text-xl sm:text-2xl font-bold text-white block tnum">
                                {{ countdown.days }}
                            </span>
                            <span class="text-[9px] font-mono text-[#94A3B8] uppercase tracking-wider">Hari</span>
                        </div>
                        <div class="bg-[#132952] border border-[#1E3A6E] rounded-[6px] p-2">
                            <span class="font-mono text-xl sm:text-2xl font-bold text-white block tnum">
                                {{ countdown.hours }}
                            </span>
                            <span class="text-[9px] font-mono text-[#94A3B8] uppercase tracking-wider">Jam</span>
                        </div>
                        <div class="bg-[#132952] border border-[#1E3A6E] rounded-[6px] p-2">
                            <span class="font-mono text-xl sm:text-2xl font-bold text-white block tnum">
                                {{ countdown.minutes }}
                            </span>
                            <span class="text-[9px] font-mono text-[#94A3B8] uppercase tracking-wider">Menit</span>
                        </div>
                        <div class="bg-[#132952] border border-[#1E3A6E] rounded-[6px] p-2">
                            <span class="font-mono text-xl sm:text-2xl font-bold text-[#FAA21B] block tnum">
                                {{ countdown.seconds }}
                            </span>
                            <span class="text-[9px] font-mono text-[#94A3B8] uppercase tracking-wider">Detik</span>
                        </div>
                    </div>
                </div>

                <!-- System Mode & Gate Info -->
                <div class="bg-[#132952] border border-[#1E3A6E] rounded-[6px] p-3 text-xs space-y-2">
                    <div class="flex justify-between items-center py-0.5 border-b border-[#1E3A6E]">
                        <span class="text-[#94A3B8]">Mode Kehadiran:</span>
                        <span class="font-medium text-white">{{ gamifikasiLabel }}</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-[#1E3A6E]">
                        <span class="text-[#94A3B8]">Sistem Penilaian:</span>
                        <span class="font-medium text-white">SPK SMART Multi-Kriteria</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-[#94A3B8]">Akses POS Stand:</span>
                        <span class="font-mono text-[#00B262]">PIN Otorisasi 6-Digit</span>
                    </div>
                </div>

                <!-- QR Mini Section -->
                <div v-if="qrCodeUrl" class="bg-white/5 border border-[#1E3A6E] rounded-[6px] p-3 flex items-center gap-3">
                    <img
                        :src="qrCodeUrl"
                        alt="QR Code Portal Acara"
                        class="w-16 h-16 bg-white p-1 rounded-[4px] shrink-0 object-contain"
                    />
                    <div class="text-xs">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-[#94A3B8] block">
                            QR Portal Publik
                        </span>
                        <p class="text-[11px] text-[#CBD5E1] mt-0.5 leading-tight">
                            Pindai untuk membuka atau membagikan halaman resmi ini langsung di smartphone.
                        </p>
                    </div>
                </div>

                <!-- Leaderboard Quick Link -->
                <Link
                    :href="`/e/${event.slug}/leaderboard`"
                    class="block w-full py-2.5 px-3 rounded-[6px] bg-[#132952] border border-[#1E3A6E] hover:border-[#00B262] text-center text-xs font-medium text-white transition-colors"
                >
                    Lihat Klasemen Stand Teraktif &rarr;
                </Link>
            </div>
        </div>
    </section>
</template>
