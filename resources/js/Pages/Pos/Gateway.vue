<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import TactileNumpad from '@/Components/Pos/TactileNumpad.vue';
import OfficialNoticeBanner from '@/Components/Pos/OfficialNoticeBanner.vue';

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const selectedEventId = ref(props.events[0]?.id || '');

const selectedEvent = computed(() => {
    return props.events.find(e => e.id === selectedEventId.value) || null;
});

const availableStands = computed(() => {
    return selectedEvent.value?.stands || [];
});

const form = useForm({
    stand_id: availableStands.value[0]?.id || '',
    pin_pos: '',
});

// Update selected stand when event changes
const onEventChange = () => {
    form.stand_id = availableStands.value[0]?.id || '';
    form.pin_pos = '';
};

const submit = (pinCode) => {
    if (pinCode) {
        form.pin_pos = pinCode;
    }
    if (!form.stand_id) {
        return;
    }
    form.post('/pos/authenticate', {
        preserveScroll: true,
        onError: () => {
            form.pin_pos = '';
        },
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] text-[#132952] font-sans antialiased selection:bg-[#00B262] selection:text-white">
        <Head title="Gerbang Kasir Stand Bazar — PATEN Kutai Timur" />

        <!-- Top Header -->
        <header class="p-4 bg-white border-b border-[#E2E8F0] flex items-center justify-between">
            <BrandMark :show-version="false" />
            <div class="flex items-center gap-3">
                <BaseBadge variant="emerald" :dot="true">
                    Terminal Kasir
                </BaseBadge>
                <Link
                    href="/"
                    class="text-xs font-mono text-[#5B6B8A] hover:text-[#132952] transition-colors"
                >
                    &larr; Beranda
                </Link>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-4">
            <div class="w-full max-w-md bg-white border border-[#CBD5E1] rounded-[10px] p-6 shadow-xs space-y-6">
                <!-- Header Badge & Title -->
                <div class="space-y-1.5 text-center">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#FAA21B]/15 border border-[#FAA21B]/30 text-[11px] font-mono text-[#B45309] font-semibold">
                        <span>📱</span>
                        <span>GERBANG KASIR STAND BAZAR</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold font-display text-[#132952] tracking-tight">
                        Buka Terminal Kasir Stand
                    </h1>
                    <p class="text-xs text-[#5B6B8A]">
                        Pilih event & nomor stand Anda, lalu ketik PIN 6-digit
                    </p>
                </div>

                <!-- Step 1 & 2: Select Event and Stand -->
                <div class="space-y-3.5">
                    <!-- Event Selector -->
                    <div class="space-y-1">
                        <label for="event-select" class="block text-xs font-mono font-medium text-[#132952]">
                            1. Pilih Event Bazar
                        </label>
                        <select
                            id="event-select"
                            v-model="selectedEventId"
                            @change="onEventChange"
                            class="w-full px-3 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] text-xs sm:text-sm focus:outline-none focus:border-[#132952] transition-colors"
                        >
                            <option v-for="ev in events" :key="ev.id" :value="ev.id">
                                {{ ev.nama_event }} ({{ ev.penyelenggara }})
                            </option>
                        </select>
                    </div>

                    <!-- Stand Selector -->
                    <div class="space-y-1">
                        <label for="stand-select" class="block text-xs font-mono font-medium text-[#132952]">
                            2. Pilih Nomor Stand Anda
                        </label>
                        <select
                            id="stand-select"
                            v-model="form.stand_id"
                            :disabled="availableStands.length === 0"
                            class="w-full px-3 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] text-xs sm:text-sm focus:outline-none focus:border-[#132952] transition-colors disabled:opacity-50"
                        >
                            <option v-if="availableStands.length === 0" value="">
                                Belum ada stand terdaftar di event ini
                            </option>
                            <option
                                v-for="st in availableStands"
                                :key="st.id"
                                :value="st.id"
                            >
                                {{ st.nomor_stand }} &mdash; {{ st.umkm_profile ? st.umkm_profile.nama_usaha : 'Stand Kosong' }} ({{ st.kategori_jualan }})
                            </option>
                        </select>
                        <p v-if="form.errors.stand_id" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.stand_id }}
                        </p>
                    </div>
                </div>

                <!-- Step 3: PIN Input using Tactile Numpad -->
                <div class="space-y-2 pt-2 border-t border-[#F1F5F9]">
                    <div class="text-center">
                        <span class="text-xs font-mono text-[#5B6B8A]">
                            3. Ketik PIN Kasir 6-Digit Stand
                        </span>
                    </div>

                    <TactileNumpad
                        v-model="form.pin_pos"
                        :error="form.errors.pin_pos"
                        :disabled="form.processing || !form.stand_id"
                        :auto-submit="true"
                        @submit="submit"
                    />
                </div>

                <!-- Perbup Compliance Banner -->
                <OfficialNoticeBanner />

                <!-- Direct Links Assistance -->
                <div class="pt-3 border-t border-[#F1F5F9] text-center space-y-1">
                    <p class="text-[11px] font-mono text-[#64748B]">
                        Lupa PIN Stand? Hubungi panitia pengelola event Anda.
                    </p>
                    <p class="text-[11px] font-mono text-[#64748B]">
                        Panitia pelaksana? <Link href="/login" class="text-[#00B262] hover:underline font-semibold">Masuk ke Dasbor PATEN</Link>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="p-4 bg-white border-t border-[#E2E8F0] text-center text-xs font-mono text-[#5B6B8A]">
            Sistem Kasir PATEN &middot; Dinas Koperasi dan UMKM Kabupaten Kutai Timur
        </footer>
    </div>
</template>
