<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

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
});

const isOnline = ref(true);

const updateOnlineStatus = () => {
    isOnline.value = navigator.onLine;
};

onMounted(() => {
    isOnline.value = navigator.onLine;
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
});

onUnmounted(() => {
    window.removeEventListener('online', updateOnlineStatus);
    window.removeEventListener('offline', updateOnlineStatus);
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#132952] font-sans antialiased">
        <!-- POS Top Navigation Bar -->
        <header class="bg-[#0C1830] text-white border-b border-[#1E3A6E] px-4 py-2.5 sticky top-0 z-30">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <BrandMark :show-version="false" class="text-white" />
                    <span class="text-[#334155] font-mono">/</span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs uppercase tracking-wider text-[#F8FAFC]">
                                {{ stand.nomor_stand }}
                            </span>
                            <BaseBadge variant="emerald" :dot="true">
                                Kasir POS
                            </BaseBadge>
                        </div>
                        <p class="text-[11px] text-[#94A3B8] font-sans truncate max-w-[180px] sm:max-w-xs">
                            {{ umkm ? umkm.nama_usaha : 'Stand Terdaftar' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Online / Offline indicator -->
                    <div class="flex items-center gap-1.5 text-[11px] font-mono">
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="isOnline ? 'bg-[#00B262]' : 'bg-[#EF4444] animate-ping'"
                        ></span>
                        <span :class="isOnline ? 'text-[#94A3B8]' : 'text-[#EF4444] font-bold'">
                            {{ isOnline ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </div>

                    <!-- Exit / Logout -->
                    <Link
                        :href="`/pos/${stand.id}/login`"
                        class="px-2.5 py-1 rounded-[4px] bg-[#1E293B] hover:bg-[#EF4444] hover:text-white text-[#94A3B8] text-[11px] font-mono transition-colors border border-[#334155] cursor-pointer"
                        title="Keluar dari sesi kasir"
                    >
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-4 sm:py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <slot />
            </div>
        </main>

        <!-- POS Colophon Footer -->
        <footer class="border-t border-[#E2E8F0] bg-white py-3 text-center text-[11px] font-mono text-[#64748B]">
            <div class="max-w-4xl mx-auto px-4">
                <span>{{ event.nama_event }} &middot; Dinas Koperasi & UKM Kutai Timur &middot; Transaksi Portofolio Bantuan</span>
            </div>
        </footer>
    </div>
</template>
