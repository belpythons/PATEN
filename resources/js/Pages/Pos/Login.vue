<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import TactileNumpad from '@/Components/Pos/TactileNumpad.vue';
import OfficialNoticeBanner from '@/Components/Pos/OfficialNoticeBanner.vue';

const props = defineProps({
    stand: {
        type: Object,
        required: true,
    },
    event: {
        type: Object,
        required: true,
    },
    umkm: {
        type: Object,
        default: null,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    pin_pos: '',
});

const submit = (pinCode) => {
    if (pinCode) {
        form.pin_pos = pinCode;
    }
    form.post(`/pos/${props.stand.id}/login-pin`, {
        preserveScroll: true,
        onError: () => {
            // Reset PIN input on failure so user can retry immediately
            form.pin_pos = '';
        },
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] text-[#132952] font-sans antialiased selection:bg-[#00B262] selection:text-white">
        <Head :title="`Login Kasir Stand ${stand.nomor_stand} — ${event.nama_event}`" />

        <!-- Minimal Top Bar -->
        <header class="p-4 bg-white border-b border-[#E2E8F0] flex items-center justify-between">
            <BrandMark :show-version="false" />
            <div class="flex items-center gap-2">
                <BaseBadge variant="emerald" :dot="true">
                    Bazar POS
                </BaseBadge>
                <Link
                    href="/"
                    class="text-xs font-mono text-[#5B6B8A] hover:text-[#132952] transition-colors"
                >
                    &larr; Beranda
                </Link>
            </div>
        </header>

        <!-- Main Login Card Area -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
            <div class="w-full max-w-sm mx-auto space-y-6">
                <!-- Stand Identity Header -->
                <div class="text-center space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-[4px] bg-[#EDF2F9] border border-[#CBD5E1]">
                        <span class="font-mono text-xs uppercase tracking-wider font-bold text-[#132952]">
                            {{ stand.nomor_stand }}
                        </span>
                        <span class="text-[#94A3B8]">&middot;</span>
                        <span class="text-xs font-sans text-[#5B6B8A]">
                            {{ stand.kategori_jualan.toUpperCase() }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold font-display text-[#132952] tracking-tight">
                        {{ umkm ? umkm.nama_usaha : 'Terminal Kasir Stand' }}
                    </h1>

                    <p class="text-xs text-[#5B6B8A] font-mono">
                        {{ event.nama_event }} &middot; {{ umkm ? umkm.nama_pemilik : 'Stan Terdaftar' }}
                    </p>
                </div>

                <!-- Touchscreen PIN Pad Container -->
                <div class="p-5 sm:p-6 rounded-[12px] bg-white border border-[#CBD5E1] shadow-xs">
                    <div class="text-center mb-4">
                        <label class="text-xs font-mono uppercase tracking-wider text-[#5B6B8A] font-semibold">
                            Masukkan PIN Kasir (6 Digit)
                        </label>
                    </div>

                    <TactileNumpad
                        v-model="form.pin_pos"
                        :disabled="form.processing"
                        :error="form.errors.pin_pos"
                        :max-length="6"
                        :auto-submit="true"
                        @submit="submit"
                    />

                    <!-- Loading indicator when processing -->
                    <div v-if="form.processing" class="mt-4 text-center">
                        <span class="inline-flex items-center gap-2 text-xs font-mono text-[#00B262] animate-pulse">
                            <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Memverifikasi PIN Stand...
                        </span>
                    </div>
                </div>

                <!-- Official Reassurance Notice Banner -->
                <OfficialNoticeBanner :compact="true" :collapsible="true" />

                <!-- Footer help text -->
                <div class="text-center text-[11px] font-mono text-[#64748B]">
                    Lupa PIN stan? Hubungi Meja Panitia Pelaksana di Venue.
                </div>
            </div>
        </main>

        <!-- Colophon -->
        <footer class="py-3 px-4 bg-white border-t border-[#E2E8F0] text-center text-[11px] font-mono text-[#5B6B8A]">
            Dinas Koperasi & UKM Kab. Kutai Timur &middot; Transaksi Portofolio Seleksi Bantuan
        </footer>
    </div>
</template>
