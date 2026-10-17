<script setup>
import { computed } from 'vue';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
        required: true,
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm']);

const estimatedSlug = computed(() => {
    if (!props.form.nama_event) return 'nama-event-anda';
    return props.form.nama_event
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
});
</script>

<template>
    <BaseModal :show="show" max-width="lg" @close="emit('close')">
        <template #header>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-[#00B262] animate-ping"></span>
                <h3 class="font-display font-bold text-base text-[#132952]">
                    Konfirmasi Publikasi Event Baru
                </h3>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Alert notice -->
            <div class="rounded-[8px] border border-[#00B262]/30 bg-[#00B262]/5 p-3.5 text-xs text-[#132952] leading-relaxed">
                <p class="font-mono uppercase font-semibold text-[#00B262] mb-1 flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-[#00B262]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pemberitahuan Publikasi Resmi
                </p>
                Setelah dikonfirmasi, event ini akan otomatis terhubung ke akun panitia Anda di Dasbor PATEN dan siap untuk alokasi stand UMKM, absensi kupon doorprize digital, serta pemantauan transaksi kasir.
            </div>

            <!-- Summary Table -->
            <div class="rounded-[8px] border border-[#E2E8F0] overflow-hidden text-xs">
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#5B6B8A] font-mono uppercase">Nama Event</span>
                    <span class="col-span-2 font-semibold text-[#132952]">{{ form.nama_event }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0]">
                    <span class="text-[#5B6B8A] font-mono uppercase">URL Portal Publik</span>
                    <span class="col-span-2 font-mono text-[#00B262] font-semibold">/e/{{ estimatedSlug }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#5B6B8A] font-mono uppercase">Penyelenggara</span>
                    <span class="col-span-2 text-[#132952]">{{ form.penyelenggara }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0]">
                    <span class="text-[#5B6B8A] font-mono uppercase">Lokasi Venue</span>
                    <span class="col-span-2 text-[#132952]">{{ form.lokasi_venue }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#5B6B8A] font-mono uppercase">Waktu Pelaksanaan</span>
                    <span class="col-span-2 text-[#132952] font-mono">
                        {{ form.tanggal_mulai }} s/d {{ form.tanggal_selesai }}
                    </span>
                </div>
                <div class="grid grid-cols-3 p-2.5">
                    <span class="text-[#5B6B8A] font-mono uppercase">Gamifikasi</span>
                    <span class="col-span-2 flex items-center gap-1.5">
                        <BaseBadge variant="navy">{{ form.gamifikasi_mode.toUpperCase() }}</BaseBadge>
                        <span v-if="form.is_awarding_active" class="text-[10px] text-[#00B262] font-mono font-bold">+ AWARDING UMKM</span>
                    </span>
                </div>
            </div>
        </div>

        <template #footer>
            <BaseButton variant="outline" size="md" :disabled="isSubmitting" @click="emit('close')">
                Periksa Kembali
            </BaseButton>
            <BaseButton variant="emerald" size="md" :loading="isSubmitting" @click="emit('confirm')">
                Publikasikan Event &rarr;
            </BaseButton>
        </template>
    </BaseModal>
</template>
