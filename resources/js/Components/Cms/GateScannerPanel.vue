<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    recentAttendees: {
        type: Array,
        default: () => [],
    },
    checkedInCount: {
        type: Number,
        default: 0,
    },
});

const ticketInput = ref('');
const isLoading = ref(false);
const checkInResult = ref(null);
const checkInError = ref('');

const submitCheckIn = async () => {
    if (!ticketInput.value.trim()) return;

    checkInError.value = '';
    checkInResult.value = null;
    isLoading.value = true;

    try {
        const response = await fetch(`/cms/${props.event.slug}/check-in?token=${props.token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CMS-Token': props.token,
            },
            body: JSON.stringify({
                nomor_tiket_doorprize: ticketInput.value.trim(),
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            checkInResult.value = data.attendee;
            ticketInput.value = '';
            // Refresh recent attendees & metrics via Inertia
            router.reload({ only: ['metrics', 'recent_attendees'] });
        } else {
            checkInError.value = data.message || 'Check-in gagal diproses.';
        }
    } catch (err) {
        checkInError.value = 'Terjadi kesalahan sistem saat memproses tiket.';
    } finally {
        isLoading.value = false;
    }
};

const formatTime = (timeStr) => {
    if (!timeStr) return '-';
    try {
        const d = new Date(timeStr);
        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WITA';
    } catch {
        return timeStr;
    }
};
</script>

<template>
    <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-6 shadow-xs mb-8">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#CBD5E1] mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#132952]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                        MODUL 2.4 / TERMINAL GATE CHECK-IN
                    </span>
                </div>
                <h3 class="text-lg font-display font-bold text-[#132952] tracking-tight">
                    Validasi Tiket Pintu Masuk Lapangan
                </h3>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Pindai atau masukkan nomor kupon warga untuk mengesahkan hak undian doorprize & mencatat kehadiran.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[6px] bg-[#EDF2F9] border border-[#CBD5E1] text-xs font-mono text-[#132952]">
                <span>Status:</span>
                <span class="font-bold text-[#00B262] flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-[#00B262] animate-pulse"></span>
                    GATE SCANNER READY
                </span>
            </div>
        </div>

        <!-- Scanner Form -->
        <form class="max-w-xl mx-auto mb-6" @submit.prevent="submitCheckIn">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <input
                        v-model="ticketInput"
                        type="text"
                        placeholder="Ketik atau scan kode tiket (misal: PATEN-DOOR-...)"
                        autofocus
                        required
                        class="w-full rounded-[6px] border-2 border-[#132952] bg-white px-4 py-2.5 text-sm font-mono font-bold text-[#132952] placeholder-[#94A3B8] focus:ring-2 focus:ring-[#132952]/20 focus:outline-none"
                    />
                    <button
                        v-if="ticketInput"
                        type="button"
                        class="absolute right-3 top-2.5 text-[#94A3B8] hover:text-[#132952] text-sm cursor-pointer"
                        @click="ticketInput = ''"
                    >
                        ✕
                    </button>
                </div>
                <BaseButton
                    type="submit"
                    variant="primary"
                    size="md"
                    :loading="isLoading"
                    class="shrink-0"
                >
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Validasi Check-in
                </BaseButton>
            </div>
        </form>

        <!-- Result Feedback Banner -->
        <!-- Success State: Emerald #00B262 -->
        <div
            v-if="checkInResult"
            class="max-w-xl mx-auto mb-6 p-4 rounded-[8px] bg-[#E6F7EF] border-2 border-[#00B262] shadow-xs"
        >
            <div class="flex items-start gap-3">
                <div class="p-1.5 rounded-full bg-[#00B262] text-white shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-mono font-bold text-[#00B262] uppercase tracking-wider">
                            CHECK-IN BERHASIL ✓
                        </span>
                        <span class="text-xs font-mono text-[#475569]">
                            {{ formatTime(checkInResult.check_in_time) }}
                        </span>
                    </div>
                    <h4 class="text-base font-display font-bold text-[#132952] mt-0.5">
                        {{ checkInResult.nama_lengkap }}
                    </h4>
                    <p class="text-xs font-mono text-[#334155] mt-1 flex flex-wrap items-center gap-2">
                        <span>Tiket: <strong class="text-[#132952]">{{ checkInResult.nomor_tiket_doorprize }}</strong></span>
                        <span>&middot;</span>
                        <span class="uppercase">Tipe: {{ checkInResult.tipe_registrasi }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Error State: Amber/Ruby -->
        <div
            v-if="checkInError"
            class="max-w-xl mx-auto mb-6 p-4 rounded-[8px] bg-[#FEE2E2] border-2 border-[#EF4444] shadow-xs"
        >
            <div class="flex items-start gap-3">
                <div class="p-1.5 rounded-full bg-[#EF4444] text-white shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <span class="text-xs font-mono font-bold text-[#EF4444] uppercase tracking-wider block">
                        CHECK-IN DITOLAK
                    </span>
                    <p class="text-xs font-medium text-[#991B1B] mt-0.5">
                        {{ checkInError }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Check-ins Stream Table -->
        <div class="pt-4 border-t border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono text-[#64748B] uppercase tracking-wider font-semibold">
                    DAFTAR PENGUNJUNG TERBARU
                </span>
                <span class="text-xs font-mono text-[#64748B]">
                    {{ checkedInCount }} Pengunjung Telah Check-in
                </span>
            </div>

            <div v-if="recentAttendees && recentAttendees.length > 0" class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#CBD5E1] bg-[#F8FAFC] text-[#64748B] font-mono text-[11px] uppercase tracking-wider">
                            <th class="py-2 px-3">Nama Pengunjung</th>
                            <th class="py-2 px-3">Nomor Tiket</th>
                            <th class="py-2 px-3">Kecamatan</th>
                            <th class="py-2 px-3">Tipe</th>
                            <th class="py-2 px-3 text-right">Status Gate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr
                            v-for="a in recentAttendees"
                            :key="a.id"
                            class="hover:bg-[#F8FAFC] transition-colors"
                        >
                            <td class="py-2.5 px-3 font-semibold text-[#132952]">
                                {{ a.nama_lengkap }}
                            </td>
                            <td class="py-2.5 px-3 font-mono text-[#132952]">
                                {{ a.nomor_tiket_doorprize }}
                            </td>
                            <td class="py-2.5 px-3 text-[#475569]">
                                {{ a.kecamatan || '-' }}
                            </td>
                            <td class="py-2.5 px-3 uppercase text-[11px] font-mono text-[#64748B]">
                                {{ a.tipe_registrasi }}
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <BaseBadge
                                    :variant="a.is_checked_in ? 'emerald' : 'navy'"
                                    :dot="true"
                                >
                                    {{ a.is_checked_in ? 'HADIR' : 'TERDAFTAR' }}
                                </BaseBadge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-else class="text-xs text-[#94A3B8] font-mono text-center py-4">
                Belum ada pengunjung terdaftar pada event ini.
            </p>
        </div>
    </div>
</template>
