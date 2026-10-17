<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AsymmetricHeroShow from '@/Components/Public/AsymmetricHeroShow.vue';
import PrecisionRundown from '@/Components/Public/PrecisionRundown.vue';
import AttendanceBox from '@/Components/Public/AttendanceBox.vue';
import TicketStubModal from '@/Components/Public/TicketStubModal.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    rundowns: {
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
</script>

<template>
    <PublicLayout>
        <Head :title="`${event.nama_event} - Portal Resmi Acara Kutai Timur`" />

        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-12">
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
