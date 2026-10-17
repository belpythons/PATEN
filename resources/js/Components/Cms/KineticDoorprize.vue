<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseModal from '@/Components/UI/BaseModal.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    doorprizeWinners: {
        type: Array,
        default: () => [],
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

const winnerCount = ref(1);
const isRolling = ref(false);
const candidateDisplay = ref('SIAP MENGUNDI...');
const currentWinners = ref([]);
const isWinnerModalOpen = ref(false);
const drawError = ref('');

let shuffleInterval = null;

const startKineticDraw = async () => {
    drawError.value = '';
    isRolling.value = true;
    currentWinners.value = [];

    // Eligible candidates from recent attendees who are checked in
    const pool = props.recentAttendees.filter((a) => a.is_checked_in && !a.is_doorprize_winner);
    const candidateNames = pool.length > 0
        ? pool.map((a) => `${a.nama_lengkap} (${a.nomor_tiket_doorprize})`)
        : ['Masyarakat Kutim 01', 'Warga Sangatta 02', 'Peserta Bazar 03', 'Pengunjung 04'];

    // Start rapid text shuffle (100ms interval)
    let idx = 0;
    shuffleInterval = setInterval(() => {
        candidateDisplay.value = candidateNames[idx % candidateNames.length];
        idx++;
    }, 80);

    try {
        const response = await fetch(`/cms/${props.event.slug}/draw-doorprize?token=${props.token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CMS-Token': props.token,
            },
            body: JSON.stringify({
                jumlah_pemenang: winnerCount.value,
            }),
        });

        const data = await response.json();

        // Let the kinetic animation roll for at least 2.5 seconds for dramatic anticipation
        setTimeout(() => {
            clearInterval(shuffleInterval);
            isRolling.value = false;

            if (response.ok && data.success && data.winners && data.winners.length > 0) {
                currentWinners.value = data.winners;
                candidateDisplay.value = data.winners.map((w) => w.nama_lengkap).join(', ');
                isWinnerModalOpen.value = true;

                // Vibrate if device supports it
                if (typeof navigator !== 'undefined' && navigator.vibrate) {
                    navigator.vibrate([100, 50, 200]);
                }

                // Reload winners table
                router.reload({ only: ['doorprize_winners', 'metrics'] });
            } else {
                drawError.value = data.message || 'Gagal mengundi doorprize.';
                candidateDisplay.value = 'UNDIAN SELESAI';
            }
        }, 2500);
    } catch (err) {
        clearInterval(shuffleInterval);
        isRolling.value = false;
        drawError.value = 'Terjadi kesalahan sistem saat mengundi doorprize.';
        candidateDisplay.value = 'ERROR KONEKSI';
    }
};

const printWinnerCertificate = () => {
    window.print();
};
</script>

<template>
    <div class="bg-white text-[#132952] border border-[#CBD5E1] rounded-[10px] p-6 shadow-xs mb-8">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E2E8F0] mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FAA21B] animate-ping"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#B45309] font-semibold">
                        MODUL 2.4 / KINETIC DOORPRIZE MACHINE
                    </span>
                </div>
                <h3 class="text-xl font-display font-bold text-[#132952] tracking-tight">
                    Mesin Pengundian Doorprize Acak Lapangan
                </h3>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Hanya pengunjung yang telah terverifikasi check-in di gate dan belum pernah menang yang berhak mengikuti undian.
                </p>
            </div>

            <!-- Total Eligible Meter -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[6px] bg-[#F8FAFC] border border-[#CBD5E1] text-xs font-mono text-[#5B6B8A]">
                <span>Pool Peserta Hadir:</span>
                <strong class="text-[#00B262]">{{ checkedInCount }} Tiket</strong>
            </div>
        </div>

        <!-- Machine Display Screen (Kinetic Rolling Box) -->
        <div class="bg-[#F8FAFC] border-2 border-[#CBD5E1] rounded-[8px] p-6 sm:p-8 text-center relative overflow-hidden mb-6 shadow-xs">
            <!-- Background Ruler Guides -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#CBD5E140_1px,transparent_1px),linear-gradient(to_bottom,#CBD5E140_1px,transparent_1px)] bg-[size:24px_24px]"></div>

            <div class="relative z-10">
                <span class="text-[10px] font-mono uppercase tracking-widest text-[#64748B] block mb-2">
                    {{ isRolling ? '/// SISTEM MENGACAK NOMOR KUPON ///' : 'STATUS MESIN UNDIAN' }}
                </span>

                <div
                    class="font-display font-bold text-2xl sm:text-4xl tracking-tight transition-all uppercase min-h-[48px] flex items-center justify-center"
                    :class="isRolling ? 'text-[#B45309] animate-pulse scale-105' : 'text-[#132952]'"
                >
                    {{ candidateDisplay }}
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-center gap-4">
                    <!-- Winner Count Selector -->
                    <div class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-[6px] border border-[#CBD5E1] text-xs font-mono">
                        <span class="text-[#64748B]">Kuota Pemenang:</span>
                        <select
                            v-model.number="winnerCount"
                            :disabled="isRolling"
                            class="bg-[#F8FAFC] text-[#132952] border border-[#CBD5E1] rounded px-2 py-0.5 text-xs font-mono focus:outline-none cursor-pointer"
                        >
                            <option :value="1">1 Orang</option>
                            <option :value="2">2 Orang</option>
                            <option :value="3">3 Orang</option>
                            <option :value="5">5 Orang</option>
                        </select>
                    </div>

                    <!-- Trigger Button: Solid Amber #FAA21B -->
                    <button
                        type="button"
                        :disabled="isRolling || checkedInCount === 0"
                        class="px-5 py-2.5 rounded-[6px] bg-[#FAA21B] hover:bg-[#E08E10] text-[#132952] font-display font-bold text-xs uppercase tracking-wider transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm cursor-pointer flex items-center gap-2"
                        @click="startKineticDraw"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ isRolling ? 'Mengacak Pemenang...' : 'Putar Undian Doorprize' }}</span>
                    </button>
                </div>

                <p v-if="drawError" class="text-xs font-mono text-[#DC2626] mt-3">
                    {{ drawError }}
                </p>
            </div>
        </div>

        <!-- Winners History Table -->
        <div class="pt-4 border-t border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono text-[#64748B] uppercase tracking-wider font-semibold">
                    RIWAYAT PEMENANG DOORPRIZE RESMI ({{ doorprizeWinners.length }})
                </span>
                <span class="text-[11px] font-mono text-[#00B262] font-semibold">
                    SAH & TERVERIFIKASI SISTEM
                </span>
            </div>

            <div v-if="doorprizeWinners && doorprizeWinners.length > 0" class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#CBD5E1] bg-[#F8FAFC] text-[#64748B] font-mono text-[11px] uppercase tracking-wider">
                            <th class="py-2.5 px-3">Nama Pemenang</th>
                            <th class="py-2.5 px-3">Nomor Tiket Kupon</th>
                            <th class="py-2.5 px-3">Kecamatan</th>
                            <th class="py-2.5 px-3">Waktu Check-in Gate</th>
                            <th class="py-2.5 px-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr
                            v-for="winner in doorprizeWinners"
                            :key="winner.id"
                            class="hover:bg-[#F8FAFC] transition-colors"
                        >
                            <td class="py-2.5 px-3 font-semibold text-[#132952]">
                                {{ winner.nama_lengkap }}
                            </td>
                            <td class="py-2.5 px-3 font-mono text-[#B45309] font-bold">
                                {{ winner.nomor_tiket_doorprize }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B]">
                                {{ winner.kecamatan || '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] font-mono text-[11px]">
                                {{ winner.check_in_time || '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] bg-[#E6F7EF] text-[#00B262] border border-[#00B262]/20 text-[10px] font-mono font-bold">
                                    PEMENANG SAH ✓
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-else class="text-xs text-[#64748B] font-mono text-center py-4">
                Belum ada pemenang undian doorprize yang ditarik untuk acara ini.
            </p>
        </div>

        <!-- Winner Reveal Modal -->
        <BaseModal
            :show="isWinnerModalOpen"
            title="Selamat Kepada Pemenang Doorprize!"
            max-width="md"
            @close="isWinnerModalOpen = false"
        >
            <div class="text-center py-4">
                <div class="inline-flex p-3 rounded-full bg-[#FEF6E8] text-[#FAA21B] mb-3">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>

                <span class="text-xs font-mono uppercase text-[#64748B] tracking-wider block">
                    HASIL RESMI UNDIAN ACARA {{ event.nama_event }}
                </span>

                <div class="mt-4 space-y-3">
                    <div
                        v-for="w in currentWinners"
                        :key="w.id"
                        class="p-4 rounded-[8px] bg-[#F8FAFC] border-2 border-[#132952] text-left"
                    >
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-[#00B262] font-bold">
                                NOMOR TIKET KUPOON:
                            </span>
                            <span class="text-xs font-mono font-bold text-[#132952]">
                                {{ w.nomor_tiket_doorprize }}
                            </span>
                        </div>
                        <h4 class="text-lg font-display font-bold text-[#132952]">
                            {{ w.nama_lengkap }}
                        </h4>
                        <p class="text-xs text-[#64748B] mt-0.5">
                            Domisili: {{ w.kecamatan || 'Kabupaten Kutai Timur' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    <BaseButton
                        variant="primary"
                        size="sm"
                        @click="printWinnerCertificate"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Berita Acara (PDF)
                    </BaseButton>

                    <BaseButton
                        variant="secondary"
                        size="sm"
                        @click="isWinnerModalOpen = false"
                    >
                        Tutup
                    </BaseButton>
                </div>
            </div>
        </BaseModal>
    </div>
</template>
