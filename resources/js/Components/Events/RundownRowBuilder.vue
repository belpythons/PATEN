<script setup>
import BaseButton from '@/Components/UI/BaseButton.vue';

defineProps({
    rundowns: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['add', 'remove']);
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between pb-1">
            <span class="text-xs font-mono uppercase tracking-[0.06em] text-[#334155] font-semibold">
                DAFTAR RUNDOWN AWAL ({{ rundowns.length }} SESI)
            </span>
            <BaseButton variant="outline" size="sm" @click="emit('add')">
                + Tambah Sesi
            </BaseButton>
        </div>

        <div v-for="(item, index) in rundowns" :key="index" class="p-3.5 rounded-[8px] border border-[#CBD5E1] bg-[#F8FAFC] space-y-3">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-2">
                <span class="text-xs font-mono font-semibold text-[#0F172A] flex items-center gap-1.5">
                    <span class="h-4 w-4 rounded-full bg-[#E2E8F0] text-[#0F172A] flex items-center justify-center text-[10px]">
                        {{ index + 1 }}
                    </span>
                    Sesi Acara
                </span>
                <button
                    v-if="rundowns.length > 1"
                    type="button"
                    class="text-xs font-mono text-[#EF4444] hover:underline cursor-pointer"
                    @click="emit('remove', index)"
                >
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-mono text-[#475569] mb-1">Judul Sesi *</label>
                    <input
                        v-model="item.judul_sesi"
                        type="text"
                        placeholder="Contoh: Pembukaan & Sambutan Bupati Kutim"
                        required
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-1.5 text-xs text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-mono text-[#475569] mb-1">Waktu Mulai *</label>
                    <input
                        v-model="item.waktu_mulai"
                        type="datetime-local"
                        required
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-1.5 text-xs font-mono text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-mono text-[#475569] mb-1">Waktu Selesai *</label>
                    <input
                        v-model="item.waktu_selesai"
                        type="datetime-local"
                        required
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-1.5 text-xs font-mono text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none"
                    />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-mono text-[#475569] mb-1">Pengisi Acara / Penanggung Jawab</label>
                    <input
                        v-model="item.pengisi_acara"
                        type="text"
                        placeholder="Contoh: Bupati Kutai Timur / Dinas Koperasi & UKM"
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-1.5 text-xs text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
