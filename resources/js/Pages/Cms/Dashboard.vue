<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import CmsLayout from '@/Layouts/CmsLayout.vue';
import SecretTokenBar from '@/Components/Cms/SecretTokenBar.vue';
import InstrumentKpiGrid from '@/Components/Cms/InstrumentKpiGrid.vue';
import RundownTableEditor from '@/Components/Cms/RundownTableEditor.vue';
import StandCardGrid from '@/Components/Cms/StandCardGrid.vue';
import StandBatchModal from '@/Components/Cms/StandBatchModal.vue';
import GateScannerPanel from '@/Components/Cms/GateScannerPanel.vue';
import KineticDoorprize from '@/Components/Cms/KineticDoorprize.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseToast from '@/Components/UI/BaseToast.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        default: '',
    },
    metrics: {
        type: Object,
        required: true,
    },
    rundowns: {
        type: Array,
        default: () => [],
    },
    stands: {
        type: Array,
        default: () => [],
    },
    recent_attendees: {
        type: Array,
        default: () => [],
    },
    doorprize_winners: {
        type: Array,
        default: () => [],
    },
    public_url: {
        type: String,
        default: '',
    },
    cms_url: {
        type: String,
        default: '',
    },
});

const isStandBatchModalOpen = ref(false);
const toastMessage = ref('');
const toastType = ref('success');
const activeSection = ref('all'); // 'all', 'rundown', 'stands', 'gate', 'doorprize'

const handleBatchSuccess = (msg) => {
    toastMessage.value = msg;
    toastType.value = 'success';
};

onMounted(() => {
    if (props.token && props.event?.slug) {
        try {
            localStorage.setItem('paten_token_' + props.event.slug, props.token);
        } catch (e) {
            // Ignore potential storage restriction
        }
    }
});
</script>

<template>
    <Head :title="`CMS Admin — ${event.nama_event}`" />

    <CmsLayout :event="event" :token="token" active-tab="dashboard">
        <!-- Toast Notification -->
        <div v-if="toastMessage" class="fixed top-4 right-4 z-50 max-w-sm">
            <BaseToast :message="toastMessage" :type="toastType" @dismiss="toastMessage = ''" />
        </div>

        <!-- Dashboard Header Banner -->
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-[#CBD5E1]">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#00B262]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                        SISTEM PATEN KUTAI TIMUR // KONSOL CMS PENGELOLA
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-display font-bold text-[#132952] tracking-tight uppercase">
                    {{ event.nama_event }}
                </h1>
                <p class="text-xs sm:text-sm text-[#475569] mt-1 flex flex-wrap items-center gap-3">
                    <span>Instansi: <strong class="text-[#132952]">{{ event.penyelenggara }}</strong></span>
                    <span>&middot;</span>
                    <span>Lokasi: <strong class="text-[#132952]">{{ event.lokasi_venue }}</strong></span>
                </p>
            </div>

            <!-- Quick Nav to Evaluations & Poster -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <Link
                    :href="`/events/${event.slug}/poster-export`"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-mono text-[#132952] hover:border-[#132952] transition-colors shadow-xs"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Poster Cetak A4</span>
                </Link>

                <Link
                    :href="token ? `/cms/${event.slug}/evaluations?token=${token}` : `/cms/${event.slug}/evaluations`"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] bg-[#132952] hover:bg-[#0D1E3D] text-white text-xs font-mono font-semibold transition-colors shadow-xs"
                >
                    <span>Evaluasi SPK & ML &rarr;</span>
                </Link>
            </div>
        </div>

        <!-- 1. Secret Token Bar -->
        <SecretTokenBar
            :event="event"
            :token="token"
            :cms-url="cms_url"
            :public-url="public_url"
        />

        <!-- 2. Instrument KPI Grid (4 Cards) -->
        <InstrumentKpiGrid :metrics="metrics" />

        <!-- 3. Section Jump Navigation (Filter View) -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 text-xs font-mono">
            <span class="text-[#64748B] uppercase tracking-wider shrink-0 mr-1">Fokus Modul:</span>
            <button
                type="button"
                class="px-3 py-1 rounded-[6px] border transition-colors cursor-pointer shrink-0"
                :class="activeSection === 'all' ? 'bg-[#132952] text-white border-[#132952] font-semibold' : 'bg-white text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                @click="activeSection = 'all'"
            >
                Semua Modul
            </button>
            <button
                type="button"
                class="px-3 py-1 rounded-[6px] border transition-colors cursor-pointer shrink-0"
                :class="activeSection === 'rundown' ? 'bg-[#132952] text-white border-[#132952] font-semibold' : 'bg-white text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                @click="activeSection = 'rundown'"
            >
                Jadwal Rundown ({{ rundowns.length }})
            </button>
            <button
                type="button"
                class="px-3 py-1 rounded-[6px] border transition-colors cursor-pointer shrink-0"
                :class="activeSection === 'stands' ? 'bg-[#132952] text-white border-[#132952] font-semibold' : 'bg-white text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                @click="activeSection = 'stands'"
            >
                Slot Stand & PIN ({{ stands.length }})
            </button>
            <button
                type="button"
                class="px-3 py-1 rounded-[6px] border transition-colors cursor-pointer shrink-0"
                :class="activeSection === 'gate' ? 'bg-[#132952] text-white border-[#132952] font-semibold' : 'bg-white text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                @click="activeSection = 'gate'"
            >
                Terminal Gate ({{ metrics.checked_in_attendees }})
            </button>
            <button
                type="button"
                class="px-3 py-1 rounded-[6px] border transition-colors cursor-pointer shrink-0"
                :class="activeSection === 'doorprize' ? 'bg-[#132952] text-white border-[#132952] font-semibold' : 'bg-white text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                @click="activeSection = 'doorprize'"
            >
                Undian Doorprize ({{ doorprize_winners.length }})
            </button>
        </div>

        <!-- 4. Rundown Table Editor -->
        <section v-show="activeSection === 'all' || activeSection === 'rundown'">
            <RundownTableEditor
                :event="event"
                :token="token"
                :rundowns="rundowns"
            />
        </section>

        <!-- 5. Stand & Facility Manager -->
        <section v-show="activeSection === 'all' || activeSection === 'stands'">
            <StandCardGrid
                :stands="stands"
                :event="event"
                :token="token"
                @open-batch-modal="isStandBatchModalOpen = true"
            />
        </section>

        <!-- 6. Gate Check-in Scanner Panel -->
        <section v-show="activeSection === 'all' || activeSection === 'gate'">
            <GateScannerPanel
                :event="event"
                :token="token"
                :recent-attendees="recent_attendees"
                :checked-in-count="metrics.checked_in_attendees"
            />
        </section>

        <!-- 7. Kinetic Doorprize Machine -->
        <section v-show="activeSection === 'all' || activeSection === 'doorprize'">
            <KineticDoorprize
                :event="event"
                :token="token"
                :doorprize-winners="doorprize_winners"
                :recent-attendees="recent_attendees"
                :checked-in-count="metrics.checked_in_attendees"
            />
        </section>

        <!-- Stand Batch Generator Modal -->
        <StandBatchModal
            :show="isStandBatchModalOpen"
            :event="event"
            :token="token"
            @close="isStandBatchModalOpen = false"
            @success="handleBatchSuccess"
        />
    </CmsLayout>
</template>
