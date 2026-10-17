<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AsymmetricHeroShow from '@/Components/Public/AsymmetricHeroShow.vue';
import PrecisionRundown from '@/Components/Public/PrecisionRundown.vue';
import AttendanceBox from '@/Components/Public/AttendanceBox.vue';
import TicketStubModal from '@/Components/Public/TicketStubModal.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    event: {
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
    qrCodeUrl: {
        type: String,
        default: '',
    },
});

const showTicketModal = ref(false);
const activeTicket = ref(null);

const handleTicketRegistered = (ticket) => {
    activeTicket.value = ticket;
    showTicketModal.value = true;
};

const closeTicketModal = () => {
    showTicketModal.value = false;
};

const navigateToCms = () => {
    if (props.event.secure_management_token) {
        window.location.href = `/cms/${props.event.slug}?token=${props.event.secure_management_token}`;
        return;
    }
    const savedToken = localStorage.getItem('paten_token_' + props.event.slug);
    if (savedToken) {
        window.location.href = `/cms/${props.event.slug}?token=${encodeURIComponent(savedToken)}`;
        return;
    }
    const token = window.prompt('Masukkan Token Rahasia Event (64-karakter) untuk masuk ke CMS Pengelola:');
    if (token && token.trim()) {
        localStorage.setItem('paten_token_' + props.event.slug, token.trim());
        window.location.href = `/cms/${props.event.slug}?token=${encodeURIComponent(token.trim())}`;
    }
};
</script>

<template>
    <PublicLayout>
        <Head :title="`${event.nama_event} - Portal Resmi Acara Kutai Timur`" />

        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-10">
            <!-- Role Guidance Bar -->
            <div class="p-3.5 rounded-[8px] bg-[#132952] text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-[#00B262] animate-pulse"></span>
                    <span class="font-mono text-[#94A3B8]">PORTAL PUBLIK PENGUNJUNG:</span>
                    <span class="font-sans text-[#CBD5E1]">Daftar tiket doorprize & pantau rundown live</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button
                        type="button"
                        @click="navigateToCms"
                        class="px-2.5 py-1 rounded-[4px] bg-[#0C1830] hover:bg-[#1E3A6E] text-[#00B262] font-mono text-[11px] font-semibold border border-[#1E3A6E] transition-colors cursor-pointer"
                    >
                        ⚙️ Akses CMS Pengelola &rarr;
                    </button>
                    <Link
                        :href="`/e/${event.slug}/leaderboard`"
                        class="px-2.5 py-1 rounded-[4px] bg-[#1E3A6E] hover:bg-[#2A4C8A] text-white font-mono text-[11px] transition-colors"
                    >
                        🏆 Leaderboard Omzet
                    </Link>
                </div>
            </div>

            <!-- 01 / Asymmetric Hero Section -->
            <AsymmetricHeroShow
                :event="event"
                :qr-code-url="qrCodeUrl"
            />

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <!-- Left Section: Precision Rundown Timeline -->
                <div class="lg:col-span-7">
                    <PrecisionRundown :rundowns="rundowns" />
                </div>

                <!-- Right Section: Sticky Attendance / Ticket Registration -->
                <div class="lg:col-span-5 lg:sticky lg:top-20">
                    <AttendanceBox
                        :event="event"
                        @registered="handleTicketRegistered"
                    />
                </div>
            </div>

            <!-- Section: Direktori Stand UMKM Lapangan -->
            <div id="stands-section" class="pt-8 border-t border-[#E2E8F0] space-y-4 scroll-mt-20">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#132952] font-semibold">
                            DIREKTORI STAND BAZAR
                        </p>
                        <h2 class="text-xl sm:text-2xl font-display font-bold text-[#132952]">
                            Daftar Stand & Pelaku Usaha UMKM
                        </h2>
                    </div>
                    <span class="text-xs font-mono text-[#64748B]">
                        {{ stands.length }} Stand Terdaftar
                    </span>
                </div>

                <!-- Stands Grid -->
                <div v-if="stands && stands.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="st in stands"
                        :key="st.id"
                        class="p-4 rounded-[8px] bg-white border border-[#CBD5E1] shadow-xs flex flex-col justify-between hover:border-[#132952] transition-colors"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <BaseBadge variant="navy" :dot="true">
                                    {{ st.nomor_stand }}
                                </BaseBadge>
                                <span class="text-[11px] font-mono uppercase text-[#64748B]">
                                    {{ st.kategori_jualan }}
                                </span>
                            </div>

                            <h3 class="font-bold text-sm text-[#132952] font-display">
                                {{ st.umkm_profile ? st.umkm_profile.nama_usaha : 'Stand Kosong (Belum Diisi)' }}
                            </h3>

                            <p class="text-xs text-[#5B6B8A] mt-0.5">
                                {{ st.umkm_profile ? `Pengelola: ${st.umkm_profile.nama_pemilik}` : 'Stand siap didaftarkan oleh pelaku UMKM' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#F1F5F9] flex items-center justify-between gap-2">
                            <a
                                :href="`/pos/${st.id}/export-menu`"
                                target="_blank"
                                class="text-[11px] font-mono text-[#5B6B8A] hover:text-[#132952] hover:underline"
                            >
                                Lihat Menu Meja ↗
                            </a>
                            <Link
                                :href="st.umkm_profile ? `/pos/${st.id}/login` : `/stands/${st.id}/assign`"
                                class="px-2.5 py-1 rounded-[4px] font-mono text-[11px] font-semibold transition-colors"
                                :class="st.umkm_profile ? 'bg-[#00B262] text-white hover:bg-[#009954]' : 'bg-[#132952] text-white hover:bg-[#0D1E3D]'"
                            >
                                {{ st.umkm_profile ? 'Terminal Kasir &rarr;' : 'Daftar Stand &rarr;' }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty Stands Notice -->
                <div v-else class="p-6 rounded-[8px] bg-white border border-[#CBD5E1] text-center space-y-2">
                    <p class="text-xs font-mono text-[#64748B]">
                        Panitia belum menambahkan slot stand bazar pada acara ini.
                    </p>
                    <a
                        :href="`/cms/${event.slug}?token=${event.secure_management_token}`"
                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[#132952] font-semibold hover:underline"
                    >
                        ⚙️ Buka CMS Pengelola untuk Tambah Slot Stand Massal &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Ticket Stub Modal -->
        <TicketStubModal
            :show="showTicketModal"
            :ticket="activeTicket"
            :event="event"
            @close="closeTicketModal"
        />
    </PublicLayout>
</template>
