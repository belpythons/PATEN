<script setup>
defineProps({
    rundowns: {
        type: Array,
        default: () => [],
    },
});

const formatTimeRange = (start, end) => {
    if (!start && !end) return 'Waktu belum ditentukan';
    if (start && end) return `${start} – ${end} WITA`;
    return `${start || end} WITA`;
};
</script>

<template>
    <section id="jadwal-rundown" class="scroll-mt-20">
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#132952]"></span>
                <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                    02 / AGENDA & JADWAL ACARA
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-display font-bold text-[#0F172A] tracking-tight">
                Rundown Presisi Kegiatan
            </h2>
            <p class="text-sm text-[#64748B] mt-1 max-w-2xl">
                Jadwal resmi seluruh sesi panggung, pembukaan pameran bazar UMKM, dan pengundian doorprize berkala.
            </p>
        </div>

        <!-- Timeline container -->
        <div v-if="rundowns && rundowns.length > 0" class="relative pl-6 sm:pl-8 ml-3 sm:ml-4 border-l-2 border-[#CBD5E1] space-y-6">
            <div
                v-for="(item, idx) in rundowns"
                :key="item.id || idx"
                class="relative group"
            >
                <!-- Timeline bullet node -->
                <div class="absolute -left-[31px] sm:-left-[39px] top-4 w-4 h-4 rounded-full bg-white border-2 border-[#132952] group-hover:scale-125 transition-transform">
                    <div class="w-1.5 h-1.5 bg-[#132952] rounded-full mx-auto my-0.5"></div>
                </div>

                <!-- Session Card -->
                <div class="bg-white border border-[#E2E8F0] rounded-[10px] p-5 shadow-xs hover:border-[#132952] transition-colors">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-[#64748B]">
                                #{{ String(item.urutan || (idx + 1)).padStart(2, '0') }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] bg-[#EDF2F9] text-[#132952] border border-[#CBD5E1] font-mono text-xs font-medium">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatTimeRange(item.waktu_mulai, item.waktu_selesai) }}
                            </span>
                        </div>
                    </div>

                    <h3 class="text-base sm:text-lg font-semibold text-[#0F172A] font-display mb-1.5">
                        {{ item.judul_sesi }}
                    </h3>

                    <div v-if="item.pengisi_acara" class="flex items-center gap-2 text-xs text-[#475569] pt-2 border-t border-[#F1F5F9]">
                        <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengisi Acara / Narasumber: <strong class="text-[#0F172A]">{{ item.pengisi_acara }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white border border-[#CBD5E1] border-dashed rounded-[10px] p-8 text-center">
            <svg class="w-10 h-10 mx-auto text-[#94A3B8] mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="font-mono text-xs uppercase tracking-wider text-[#64748B]">
                Agenda Belum Dipublikasikan
            </p>
            <p class="text-xs text-[#94A3B8] mt-1 max-w-sm mx-auto">
                Susunan jadwal rundown kegiatan akan segera dirilis oleh panitia pelaksana event.
            </p>
        </div>
    </section>
</template>
