<script setup>
import { ref, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import CmsLayout from '@/Layouts/CmsLayout.vue';
import SpkHeaderMetrics from '@/Components/Spk/SpkHeaderMetrics.vue';
import SpkFilterToolbar from '@/Components/Spk/SpkFilterToolbar.vue';
import SawMatrixTable from '@/Components/Spk/SawMatrixTable.vue';
import ExecutiveReportModal from '@/Components/Spk/ExecutiveReportModal.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        default: '',
    },
    evaluations: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    cms_url: {
        type: String,
        default: '',
    },
});

const isEvaluating = ref(false);
const isReportModalOpen = ref(false);
const statusMessage = ref('');
const errorMessage = ref('');

// Filter State
const filters = ref({
    search: '',
    kecamatan: '',
    kategori: '',
    status: '',
    program: '',
});

const resetFilters = () => {
    filters.value = {
        search: '',
        kecamatan: '',
        kategori: '',
        status: '',
        program: '',
    };
};

// Filtered Evaluations computation
const filteredEvaluations = computed(() => {
    return (props.evaluations || []).filter((item) => {
        // Search text filter
        if (filters.value.search) {
            const query = filters.value.search.toLowerCase();
            const standNo = item.stand?.nomor_stand?.toLowerCase() || '';
            const namaUsaha = item.umkm_profile?.nama_usaha?.toLowerCase() || '';
            const namaPemilik = item.umkm_profile?.nama_pemilik?.toLowerCase() || '';
            const noKk = item.umkm_profile?.no_kk?.toLowerCase() || '';
            const nik = item.umkm_profile?.nik?.toLowerCase() || '';

            if (
                !standNo.includes(query) &&
                !namaUsaha.includes(query) &&
                !namaPemilik.includes(query) &&
                !noKk.includes(query) &&
                !nik.includes(query)
            ) {
                return false;
            }
        }

        // Kecamatan filter
        if (filters.value.kecamatan) {
            if (item.umkm_profile?.kecamatan !== filters.value.kecamatan) {
                return false;
            }
        }

        // Kategori filter
        if (filters.value.kategori) {
            if (item.stand?.kategori_jualan !== filters.value.kategori) {
                return false;
            }
        }

        // Status filter
        if (filters.value.status) {
            if (filters.value.status === 'prioritas') {
                if (!item.is_prioritas_hibah_200) return false;
            } else if (filters.value.status === 'lolos') {
                if (item.catatan_dinas && item.catatan_dinas.toLowerCase().includes('diskualifikasi')) return false;
            } else if (filters.value.status === 'diskualifikasi') {
                if (!item.catatan_dinas || !item.catatan_dinas.toLowerCase().includes('diskualifikasi')) return false;
            }
        }

        // Program filter
        if (filters.value.program) {
            if (item.rekomendasi_program !== filters.value.program) {
                return false;
            }
        }

        return true;
    });
});

// Trigger SPK & Perbup Guardrail calculation
const runEvaluation = () => {
    isEvaluating.value = true;
    statusMessage.value = '';
    errorMessage.value = '';

    const url = props.token
        ? `/cms/${props.event.slug}/evaluate-spk?token=${props.token}`
        : `/cms/${props.event.slug}/evaluate-spk`;

    router.post(
        url,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                statusMessage.value = 'Kalkulasi SAW SPK dan verifikasi Guardrail Perbup Kutim berhasil dieksekusi!';
                isEvaluating.value = false;
                setTimeout(() => {
                    if (statusMessage.value) statusMessage.value = '';
                }, 4000);
            },
            onError: (errs) => {
                errorMessage.value = 'Gagal mengeksekusi evaluasi: ' + Object.values(errs).join(', ');
                isEvaluating.value = false;
            },
        }
    );
};
</script>

<template>
    <CmsLayout :event="event" :token="token" active-tab="evaluations">
        <Head :title="`Evaluasi SPK & Rekomendasi Bantuan — ${event.nama_event}`" />

        <div class="space-y-6">
            <!-- Flash Message Feedback -->
            <transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="statusMessage"
                    class="p-3.5 rounded-[8px] bg-[#00B262] text-white flex items-center justify-between shadow-md"
                >
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ statusMessage }}</span>
                    </div>
                    <button
                        type="button"
                        class="text-white hover:text-[#E2E8F0] p-1 cursor-pointer"
                        @click="statusMessage = ''"
                    >
                        ✕
                    </button>
                </div>
            </transition>

            <div
                v-if="errorMessage"
                class="p-3.5 rounded-[8px] bg-[#FEF2F2] border border-[#FCA5A5] text-[#991B1B] text-xs font-mono"
            >
                {{ errorMessage }}
            </div>

            <!-- Workbench Header & Summary KPIs -->
            <SpkHeaderMetrics
                :summary="summary"
                :is-evaluating="isEvaluating"
                @run-evaluation="runEvaluation"
                @open-report="isReportModalOpen = true"
            />

            <!-- Filter Toolbar -->
            <SpkFilterToolbar
                v-model="filters"
                :total-count="evaluations.length"
                :filtered-count="filteredEvaluations.length"
                @reset="resetFilters"
            />

            <!-- 5-Criteria SAW Matrix Data Table -->
            <SawMatrixTable :evaluations="filteredEvaluations" />
        </div>

        <!-- Executive Report Print Modal -->
        <ExecutiveReportModal
            :show="isReportModalOpen"
            :event="event"
            :evaluations="evaluations"
            :summary="summary"
            @close="isReportModalOpen = false"
        />
    </CmsLayout>
</template>
