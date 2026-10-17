<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import PosLayout from '@/Layouts/PosLayout.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import FastMenuSelector from '@/Components/Pos/FastMenuSelector.vue';
import QuantityStepper from '@/Components/Pos/QuantityStepper.vue';
import QuickAddMenuModal from '@/Components/Pos/QuickAddMenuModal.vue';

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
    menus: {
        type: Array,
        default: () => [],
    },
    recent_transactions: {
        type: Array,
        default: () => [],
    },
});

const isAddMenuModalOpen = ref(false);
const statusMessage = ref('');
const errorMessage = ref('');

// Fast POS Transaction Input Form
const txForm = useForm({
    menu_id: props.menus && props.menus.length ? props.menus[0].id : '',
    qty: 1,
    metode_pembayaran: 'cash',
});

// Automatically fallback to first available menu if current choice is invalid
const selectedMenu = computed(() => {
    return (props.menus || []).find(m => m.id === txForm.menu_id) || null;
});

const subtotalPreview = computed(() => {
    if (!selectedMenu.value) return 0;
    return Number(selectedMenu.value.harga || 0) * Number(txForm.qty || 1);
});

const triggerHaptic = (duration = 50) => {
    if (typeof navigator !== 'undefined' && navigator.vibrate) {
        try {
            navigator.vibrate(duration);
        } catch (e) {
            // Ignore
        }
    }
};

const submitTransaction = () => {
    statusMessage.value = '';
    errorMessage.value = '';

    if (!txForm.menu_id) {
        errorMessage.value = 'Silakan pilih menu produk terlebih dahulu.';
        return;
    }

    triggerHaptic(60);

    txForm.post(`/pos/${props.stand.id}/transactions`, {
        preserveScroll: true,
        onSuccess: () => {
            const recordedItem = selectedMenu.value?.nama_item || 'Item';
            const recordedQty = txForm.qty;
            const recordedSubtotal = subtotalPreview.value;

            statusMessage.value = `Berhasil! ${recordedItem} (x${recordedQty}) Rp ${recordedSubtotal.toLocaleString('id-ID')} tersimpan.`;
            // Auto reset quantity to 1 for ultra fast next transaction (<= 3s ergonomics)
            txForm.qty = 1;

            // Auto dismiss notification after 4 seconds
            setTimeout(() => {
                if (statusMessage.value) statusMessage.value = '';
            }, 4000);
        },
        onError: (errs) => {
            errorMessage.value = 'Gagal mencatat: ' + Object.values(errs).join(', ');
        },
    });
};
</script>

<template>
    <PosLayout :stand="stand" :event="event" :umkm="umkm">
        <Head :title="`POS Kasir ${stand.nomor_stand} — ${event.nama_event}`" />

        <div class="space-y-5">
            <!-- Shift Header & Live Metrics -->
            <div class="p-4 sm:p-5 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold uppercase text-[#132952]">
                                {{ stand.nomor_stand }}
                            </span>
                            <span class="text-[#CBD5E1]">&middot;</span>
                            <span class="text-xs font-medium text-[#00B262]">
                                Shift Aktif
                            </span>
                            <span class="text-[#CBD5E1]">&middot;</span>
                            <span class="text-xs text-[#64748B] font-mono">
                                {{ stand.kategori_jualan.toUpperCase() }}
                            </span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-bold font-display text-[#132952] mt-0.5">
                            {{ umkm ? umkm.nama_usaha : 'Stand UMKM' }}
                        </h1>
                        <p class="text-xs text-[#64748B] font-mono">
                            Pengelola: {{ umkm ? umkm.nama_pemilik : 'Pelaku Usaha' }} &middot; {{ event.nama_event }}
                        </p>
                    </div>

                    <!-- Right Affordance: Link to Menu Printable Sheet -->
                    <div class="flex items-center gap-2">
                        <a
                            :href="`/pos/${stand.id}/export-menu`"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-[#F8FAFC] hover:bg-[#F1F5F9] text-xs font-mono text-[#132952] transition-colors"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Menu Meja</span>
                        </a>
                    </div>
                </div>

                <!-- Shift Metrics KPI Grid -->
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-[#E2E8F0]">
                    <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                            Total Omzet Stand
                        </p>
                        <p class="mt-1 text-lg sm:text-2xl font-bold font-mono text-[#132952]">
                            Rp {{ Number(stand.total_omzet_terekam || 0).toLocaleString('id-ID') }}
                        </p>
                    </div>

                    <div class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-mono uppercase tracking-wider text-[#64748B]">
                            Total Transaksi
                        </p>
                        <p class="mt-1 text-lg sm:text-2xl font-bold font-mono text-[#00B262]">
                            {{ stand.total_transaksi_terekam || 0 }} <span class="text-xs font-normal text-[#64748B]">Struk</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Transaction Notification Feedback Banner -->
            <transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="statusMessage"
                    class="p-3.5 rounded-[8px] bg-[#00B262] text-white flex items-center justify-between shadow-md"
                >
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ statusMessage }}</span>
                    </div>
                    <button
                        type="button"
                        class="text-white hover:text-[#E2E8F0] p-1 cursor-pointer"
                        @click="statusMessage = ''"
                    >
                        ✕
                    </button>
                </div>
            </transition>

            <div
                v-if="errorMessage"
                class="p-3.5 rounded-[8px] bg-[#FEF2F2] border border-[#FCA5A5] text-[#991B1B] text-xs font-mono"
            >
                {{ errorMessage }}
            </div>

            <!-- Main Cashier Instrument Card (Light Hallmark Style #CBD5E1 / White) -->
            <div class="p-5 sm:p-6 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs text-[#132952] space-y-6">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-[#00B262] animate-pulse"></span>
                        <h2 class="text-sm font-bold font-mono uppercase tracking-wider text-[#132952]">
                            Terminal Input Transaksi (&le; 3 Detik)
                        </h2>
                    </div>
                    <span class="text-[11px] font-mono text-[#64748B]">
                        Auto-Reset Kuantitas
                    </span>
                </div>

                <form @submit.prevent="submitTransaction" class="space-y-6">
                    <!-- Step 1: Menu Picker with Fast Chips -->
                    <FastMenuSelector
                        :menus="menus"
                        v-model="txForm.menu_id"
                        @open-add-menu="isAddMenuModalOpen = true"
                    />

                    <!-- Step 2: Tactile Quantity Stepper -->
                    <QuantityStepper
                        v-model="txForm.qty"
                        :min="1"
                        :max="100"
                    />

                    <!-- Step 3: Payment Method High-Contrast Toggle -->
                    <div class="space-y-2">
                        <label class="block text-xs font-mono uppercase tracking-wider font-semibold text-[#132952]">
                            Metode Pembayaran
                        </label>

                        <div class="grid grid-cols-2 gap-3">
                            <!-- Cash / Tunai Button -->
                            <button
                                type="button"
                                class="h-13 sm:h-14 rounded-[8px] border-2 font-mono text-sm font-bold flex items-center justify-center gap-2 transition-all cursor-pointer active:scale-95"
                                :class="[
                                    txForm.metode_pembayaran === 'cash'
                                        ? 'bg-[#00B262] border-[#00B262] text-white shadow-sm'
                                        : 'bg-[#F8FAFC] border-[#CBD5E1] text-[#475569] hover:border-[#132952] hover:text-[#132952]'
                                ]"
                                @click="txForm.metode_pembayaran = 'cash'"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>TUNAI (CASH)</span>
                            </button>

                            <!-- QRIS / Digital Button -->
                            <button
                                type="button"
                                class="h-13 sm:h-14 rounded-[8px] border-2 font-mono text-sm font-bold flex items-center justify-center gap-2 transition-all cursor-pointer active:scale-95"
                                :class="[
                                    txForm.metode_pembayaran === 'qris'
                                        ? 'bg-[#00B262] border-[#00B262] text-white shadow-sm'
                                        : 'bg-[#F8FAFC] border-[#CBD5E1] text-[#475569] hover:border-[#132952] hover:text-[#132952]'
                                ]"
                                @click="txForm.metode_pembayaran = 'qris'"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>QRIS / DIGITAL</span>
                            </button>
                        </div>
                    </div>

                    <!-- Subtotal Preview Box -->
                    <div class="p-3.5 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1] flex items-center justify-between">
                        <span class="text-xs font-mono uppercase tracking-wider text-[#64748B]">
                            Subtotal Transaksi:
                        </span>
                        <span class="text-xl sm:text-2xl font-mono font-bold text-[#00B262]">
                            Rp {{ subtotalPreview.toLocaleString('id-ID') }}
                        </span>
                    </div>

                    <!-- Step 4: Giant Execution Button (Simpan Transaksi) -->
                    <button
                        type="submit"
                        :disabled="txForm.processing || !menus.length"
                        class="w-full h-15 sm:h-16 rounded-[8px] bg-[#00B262] hover:bg-[#009E57] active:bg-[#008A4C] active:scale-98 text-white font-mono text-lg font-bold uppercase tracking-wider transition-all duration-75 flex items-center justify-center gap-2 cursor-pointer shadow-lg disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span v-if="txForm.processing" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            MENCATAT TRANSAKSI...
                        </span>
                        <span v-else class="flex items-center gap-2">
                            <span>SIMPAN TRANSAKSI (TAP)</span>
                            <span class="text-xs bg-white/20 px-2 py-0.5 rounded-[4px] font-sans">
                                [ENTER]
                            </span>
                        </span>
                    </button>
                </form>
            </div>

            <!-- Recent 10 Transactions Audit Feed -->
            <div class="p-4 sm:p-5 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-[#132952] flex items-center gap-2">
                        <span>Riwayat 10 Transaksi Terakhir Stand</span>
                        <span class="text-[10px] font-normal text-[#64748B]">
                            ({{ recent_transactions.length }} tercatat)
                        </span>
                    </h3>
                </div>

                <div v-if="recent_transactions && recent_transactions.length > 0" class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] text-[#64748B] font-mono uppercase text-[11px]">
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Menu Produk</th>
                                <th class="py-2.5 px-3 text-center">Porsi</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                                <th class="py-2.5 px-3 text-center">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-mono">
                            <tr
                                v-for="tx in recent_transactions"
                                :key="tx.id"
                                class="hover:bg-[#F8FAFC] transition-colors"
                            >
                                <td class="py-2.5 px-3 text-[#64748B] whitespace-nowrap">
                                    {{ new Date(tx.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}
                                </td>
                                <td class="py-2.5 px-3 font-sans font-medium text-[#132952]">
                                    {{ tx.menu ? tx.menu.nama_item : 'Menu Stand' }}
                                </td>
                                <td class="py-2.5 px-3 text-center text-[#132952]">
                                    {{ tx.qty }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-[#132952]">
                                    Rp {{ Number(tx.subtotal).toLocaleString('id-ID') }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <BaseBadge
                                        :variant="tx.metode_pembayaran === 'qris' ? 'navy' : 'emerald'"
                                        size="sm"
                                    >
                                        {{ tx.metode_pembayaran.toUpperCase() }}
                                    </BaseBadge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="text-center py-6 text-xs text-[#94A3B8] font-mono">
                    Belum ada riwayat transaksi pada shift ini.
                </div>
            </div>
        </div>

        <!-- Quick Add Menu Modal -->
        <QuickAddMenuModal
            :show="isAddMenuModalOpen"
            :stand-id="stand.id"
            @close="isAddMenuModalOpen = false"
            @created="isAddMenuModalOpen = false"
        />
    </PosLayout>
</template>
