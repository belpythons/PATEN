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
                <span class="h-2 w-2 rounded-full bg-[#F59E0B] animate-ping"></span>
                <h3 class="font-display font-bold text-base text-[#0F172A]">
                    Konfirmasi Penguncian & Penerbitan Event
                </h3>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Alert notice -->
            <div class="rounded-[6px] border border-[#CBD5E1] bg-[#F8FAFC] p-3.5 text-xs text-[#334155] leading-relaxed">
                <p class="font-mono uppercase font-semibold text-[#0F172A] mb-1 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Pemberitahuan Kriptografis
                </p>
                Setelah dikonfirmasi, sistem akan mengunci data dasar acara dan men-generate <strong>Token Manajemen Rahasia 64-karakter</strong> unik untuk hak akses CMS penyelenggara.
            </div>

            <!-- Summary Table -->
            <div class="rounded-[8px] border border-[#E2E8F0] overflow-hidden text-xs">
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#64748B] font-mono uppercase">Nama Event</span>
                    <span class="col-span-2 font-semibold text-[#0F172A]">{{ form.nama_event }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0]">
                    <span class="text-[#64748B] font-mono uppercase">URL Slug Publik</span>
                    <span class="col-span-2 font-mono text-[#2563EB]">/e/{{ estimatedSlug }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#64748B] font-mono uppercase">Penyelenggara</span>
                    <span class="col-span-2 text-[#0F172A]">{{ form.penyelenggara }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0]">
                    <span class="text-[#64748B] font-mono uppercase">Lokasi Venue</span>
                    <span class="col-span-2 text-[#0F172A]">{{ form.lokasi_venue }}</span>
                </div>
                <div class="grid grid-cols-3 p-2.5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <span class="text-[#64748B] font-mono uppercase">Waktu Pelaksanaan</span>
                    <span class="col-span-2 text-[#0F172A] font-mono">
                        {{ form.tanggal_mulai }} s/d {{ form.tanggal_selesai }}
                    </span>
                </div>
                <div class="grid grid-cols-3 p-2.5">
                    <span class="text-[#64748B] font-mono uppercase">Gamifikasi</span>
                    <span class="col-span-2 flex items-center gap-1.5">
                        <BaseBadge variant="cobalt">{{ form.gamifikasi_mode.toUpperCase() }}</BaseBadge>
                        <span v-if="form.is_awarding_active" class="text-[10px] text-[#059669] font-mono font-bold">+ AWARDING UMKM</span>
                    </span>
                </div>
            </div>
        </div>

        <template #footer>
            <BaseButton variant="outline" size="md" :disabled="isSubmitting" @click="emit('close')">
                Periksa Kembali
            </BaseButton>
            <BaseButton variant="primary" size="md" :loading="isSubmitting" @click="emit('confirm')">
                Kunci & Terbitkan Event &rarr;
            </BaseButton>
        </template>
    </BaseModal>
</template>
