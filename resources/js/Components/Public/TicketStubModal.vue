<script setup>
import { ref } from 'vue';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    ticket: {
        type: Object,
        default: null,
    },
    event: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const copySuccess = ref(false);

const copyTicketCode = async () => {
    if (!props.ticket?.nomor_tiket_doorprize) return;

    try {
        await navigator.clipboard.writeText(props.ticket.nomor_tiket_doorprize);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 2500);
    } catch (e) {
        // Fallback for older browsers or insecure contexts
        const textarea = document.createElement('textarea');
        textarea.value = props.ticket.nomor_tiket_doorprize;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 2500);
    }
};

const printTicket = () => {
    window.print();
};
</script>

<template>
    <BaseModal
        :show="show"
        max-width="md"
        @close="emit('close')"
    >
        <template #header>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#2563EB]"></span>
                <span class="text-xs font-mono uppercase tracking-wider text-[#64748B]">
                    E-Tiket & Kupon Resmi
                </span>
            </div>
        </template>

        <div v-if="ticket" class="space-y-5">
            <!-- Event & Header Info -->
            <div class="text-center pb-2">
                <h4 class="font-display font-bold text-lg text-[#0F172A] leading-snug">
                    {{ event.nama_event || 'Gebyar UMKM Kutai Timur' }}
                </h4>
                <p class="text-xs text-[#64748B] mt-1">
                    {{ event.penyelenggara || 'Dinas Koperasi & UKM Kab. Kutai Timur' }}
                </p>
                <div class="mt-2 flex items-center justify-center gap-2">
                    <BaseBadge variant="cobalt" size="sm">
                        {{ ticket.tipe_registrasi === 'htm' ? 'Tiket Masuk (HTM)' : 'Kupon Doorprize' }}
                    </BaseBadge>
                    <BaseBadge variant="emerald" size="sm">
                        Terverifikasi
                    </BaseBadge>
                </div>
            </div>

            <!-- Big Monospace Ticket Code Box -->
            <div class="bg-[#EFF6FF] border border-[#BFDBFE] rounded-[6px] p-4 text-center">
                <div class="text-[10px] font-mono uppercase tracking-wider text-[#1D4ED8] font-semibold mb-1">
                    Nomor Unik Tiket / Kupon
                </div>
                <div class="font-mono text-2xl font-bold tracking-widest text-[#2563EB] select-all">
                    {{ ticket.nomor_tiket_doorprize }}
                </div>
            </div>

            <!-- Visitor Details Table -->
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-[6px] p-3 text-xs space-y-2">
                <div class="flex justify-between items-center py-0.5 border-b border-[#E2E8F0]">
                    <span class="text-[#64748B]">Nama Pemegang:</span>
                    <span class="font-medium text-[#0F172A]">{{ ticket.nama_lengkap }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5 border-b border-[#E2E8F0]">
                    <span class="text-[#64748B]">Kategori:</span>
                    <span class="font-mono uppercase text-[#0F172A]">{{ ticket.tipe_registrasi }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-[#64748B]">Status Check-In:</span>
                    <span class="text-[#64748B] font-mono text-[11px]">Belum Digunakan (Di Gate)</span>
                </div>
            </div>

            <!-- Dashed Tear Divider with Ticket Notches -->
            <div class="relative py-2">
                <div class="border-b-2 border-dashed border-[#CBD5E1]"></div>
                <!-- Left Notch -->
                <div class="absolute -left-7 top-1/2 -translate-y-1/2 w-4 h-4 bg-[#F8FAFC] border-r border-[#CBD5E1] rounded-r-full"></div>
                <!-- Right Notch -->
                <div class="absolute -right-7 top-1/2 -translate-y-1/2 w-4 h-4 bg-[#F8FAFC] border-l border-[#CBD5E1] rounded-l-full"></div>
            </div>

            <!-- QR Code Section -->
            <div class="text-center space-y-2">
                <div v-if="ticket.qr_data_url" class="inline-block p-3 bg-white border border-[#CBD5E1] rounded-[6px] shadow-xs">
                    <img
                        :src="ticket.qr_data_url"
                        :alt="`QR Tiket ${ticket.nomor_tiket_doorprize}`"
                        class="w-40 h-40 mx-auto object-contain"
                    />
                </div>
                <p class="text-xs text-[#475569] leading-relaxed max-w-xs mx-auto">
                    Tunjukkan QR Code ini kepada panitia saat berada di pintu masuk (gate) untuk check-in kehadiran.
                </p>
            </div>
        </div>

        <template #footer>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 w-full">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <BaseButton
                        variant="secondary"
                        size="sm"
                        class="w-full sm:w-auto"
                        @click="copyTicketCode"
                    >
                        <span v-if="copySuccess" class="text-[#2563EB] font-bold">
                            Tersalin! ✓
                        </span>
                        <span v-else>
                            Salin Kode
                        </span>
                    </BaseButton>
                    <BaseButton
                        variant="outline"
                        size="sm"
                        class="w-full sm:w-auto"
                        @click="printTicket"
                    >
                        Cetak / Simpan
                    </BaseButton>
                </div>
                <BaseButton
                    variant="primary"
                    size="sm"
                    class="w-full sm:w-auto"
                    @click="emit('close')"
                >
                    Selesai
                </BaseButton>
            </div>
        </template>
    </BaseModal>
</template>
