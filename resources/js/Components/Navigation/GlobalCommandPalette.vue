<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const search = ref('');
const selectedIndex = ref(0);
const inputRef = ref(null);

const commands = [
    {
        id: 'home',
        category: 'Navigasi',
        title: 'Beranda Sistem PATEN',
        subtitle: 'Halaman selamat datang dan ikhtisar sistem',
        action: () => router.visit('/'),
        shortcut: 'H',
    },
    {
        id: 'create-event',
        category: 'Event',
        title: 'Buat Event Baru',
        subtitle: 'Inisialisasi form event, skema warna & rundown',
        action: () => router.visit('/events/create'),
        shortcut: 'E',
    },
    {
        id: 'health',
        category: 'Sistem',
        title: 'Diagnostik Sistem & Database',
        subtitle: 'Cek koneksi Supabase PostgreSQL dan ekstensi',
        action: () => window.open('/health', '_blank'),
        shortcut: 'D',
    },
    {
        id: 'help-perbup',
        category: 'Regulasi',
        title: 'Pedoman Regulasi Perbup Kutim',
        subtitle: 'Aturan 1 KK 1 Penerima & Syarat Hibah Modal APBD',
        action: () => alert('Regulasi Perbup Kutim: 1 KK maksimal 1 penerima bantuan, NIB aktif >= 6 bulan, domisili Kutai Timur, dan bukan ASN/TNI/Polri.'),
        shortcut: 'R',
    },
];

const filteredCommands = computed(() => {
    if (!search.value.trim()) return commands;
    const q = search.value.toLowerCase();
    return commands.filter(cmd =>
        cmd.title.toLowerCase().includes(q) ||
        cmd.subtitle.toLowerCase().includes(q) ||
        cmd.category.toLowerCase().includes(q)
    );
});

const executeCommand = (cmd) => {
    emit('close');
    cmd.action();
};

const handleKeyDown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (props.isOpen) {
            emit('close');
        } else {
            openPalette();
        }
        return;
    }

    if (!props.isOpen) return;

    if (e.key === 'Escape') {
        emit('close');
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (selectedIndex.value < filteredCommands.value.length - 1) {
            selectedIndex.value++;
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (selectedIndex.value > 0) {
            selectedIndex.value--;
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        const cmd = filteredCommands.value[selectedIndex.value];
        if (cmd) {
            executeCommand(cmd);
        }
    }
};

const openPalette = () => {
    search.value = '';
    selectedIndex.value = 0;
    nextTick(() => {
        if (inputRef.value) {
            inputRef.value.focus();
        }
    });
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <div v-if="isOpen" class="relative z-50" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-[#0F172A]/40 backdrop-blur-xs transition-opacity"
            @click="emit('close')"
        ></div>

        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
            <div class="flex min-h-full items-start justify-center pt-8 sm:pt-14 text-center">
                <div
                    class="relative transform overflow-hidden rounded-[10px] bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-[#CBD5E1]"
                    @click.stop
                >
                    <!-- Search input -->
                    <div class="flex items-center border-b border-[#E2E8F0] px-4 py-3 bg-[#F8FAFC]">
                        <svg class="h-4 w-4 text-[#64748B] mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            ref="inputRef"
                            v-model="search"
                            type="text"
                            placeholder="Ketik perintah atau navigasi cepat..."
                            class="w-full bg-transparent text-sm text-[#0F172A] placeholder-[#94A3B8] focus:outline-none font-sans"
                            autofocus
                        />
                        <kbd class="text-[10px] font-mono uppercase bg-white border border-[#CBD5E1] px-1.5 py-0.5 rounded text-[#64748B]">
                            ESC
                        </kbd>
                    </div>

                    <!-- Command list -->
                    <div class="max-h-80 overflow-y-auto p-2">
                        <div v-if="filteredCommands.length === 0" class="py-8 text-center text-xs font-mono text-[#64748B]">
                            Tidak ada perintah yang cocok dengan "{{ search }}".
                        </div>
                        <button
                            v-for="(cmd, index) in filteredCommands"
                            :key="cmd.id"
                            type="button"
                            class="w-full flex items-center justify-between rounded-[6px] px-3 py-2.5 text-left text-sm transition-colors cursor-pointer group"
                            :class="selectedIndex === index ? 'bg-[#EFF6FF] text-[#2563EB]' : 'text-[#0F172A] hover:bg-[#F8FAFC]'"
                            @mouseenter="selectedIndex = index"
                            @click="executeCommand(cmd)"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 rounded bg-white border border-[#CBD5E1] text-[#475569] shrink-0">
                                    {{ cmd.category }}
                                </span>
                                <div class="truncate">
                                    <p class="font-medium text-xs sm:text-sm truncate">
                                        {{ cmd.title }}
                                    </p>
                                    <p class="text-[11px] text-[#64748B] truncate">
                                        {{ cmd.subtitle }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-mono text-[#94A3B8] shrink-0 pl-2">
                                {{ cmd.shortcut }}
                            </span>
                        </button>
                    </div>

                    <!-- Footer hint -->
                    <div class="border-t border-[#E2E8F0] bg-[#F8FAFC] px-4 py-2 flex items-center justify-between text-[11px] font-mono text-[#64748B]">
                        <div class="flex items-center gap-3">
                            <span><kbd class="bg-white border border-[#CBD5E1] px-1 rounded">↑</kbd> <kbd class="bg-white border border-[#CBD5E1] px-1 rounded">↓</kbd> Navigasi</span>
                            <span><kbd class="bg-white border border-[#CBD5E1] px-1 rounded">↵</kbd> Eksekusi</span>
                        </div>
                        <span>PATEN Kutai Timur</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
