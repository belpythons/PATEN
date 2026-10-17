<script setup>
import { ref, computed } from 'vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    stands: {
        type: Array,
        default: () => [],
    },
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['openBatchModal']);

const searchQuery = ref('');
const categoryFilter = ref('');
const statusFilter = ref('');
const copiedPinId = ref(null);
const copiedLinkId = ref(null);

const filteredStands = computed(() => {
    return props.stands.filter((s) => {
        const matchesSearch = !searchQuery.value ||
            s.nomor_stand.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (s.nama_usaha && s.nama_usaha.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (s.nama_pemilik && s.nama_pemilik.toLowerCase().includes(searchQuery.value.toLowerCase()));

        const matchesCategory = !categoryFilter.value || s.kategori_jualan === categoryFilter.value;

        const isAssigned = !!(s.nama_usaha || s.nama_pemilik);
        const matchesStatus = !statusFilter.value ||
            (statusFilter.value === 'assigned' && isAssigned) ||
            (statusFilter.value === 'unassigned' && !isAssigned);

        return matchesSearch && matchesCategory && matchesStatus;
    });
});

const getAssignUrl = (standId) => {
    return `${window.location.origin}/stands/${standId}/assign`;
};

const copyPin = (stand) => {
    navigator.clipboard.writeText(stand.pin_pos).then(() => {
        copiedPinId.value = stand.id;
        setTimeout(() => {
            copiedPinId.value = null;
        }, 2000);
    });
};

const copyAssignLink = (stand) => {
    const url = getAssignUrl(stand.id);
    navigator.clipboard.writeText(url).then(() => {
        copiedLinkId.value = stand.id;
        setTimeout(() => {
            copiedLinkId.value = null;
        }, 2000);
    });
};

const getWhatsAppShareUrl = (stand) => {
    const url = getAssignUrl(stand.id);
    const text = `Halo, berikut adalah tautan registrasi dan profil stand UMKM Anda untuk acara ${props.event.nama_event}: ${url} . Nomor Stand: ${stand.nomor_stand}, PIN Kasir: ${stand.pin_pos}. Mohon segera melengkapi data legalitas NIB/KK sesuai Perbup Kutim.`;
    return `https://wa.me/?text=${encodeURIComponent(text)}`;
};
</script>

<template>
    <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-6 shadow-xs mb-8">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#CBD5E1] mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#132952]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                        MODUL 2.3 / MANAJEMEN STAND & FASILITAS
                    </span>
                </div>
                <h3 class="text-lg font-display font-bold text-[#132952] tracking-tight">
                    Kavling Stand & Kredensial Kasir POS
                </h3>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Kelola slot stand peserta, PIN kasir 6 digit rahasia, dan distribusi tautan profil UMKM.
                </p>
            </div>

            <BaseButton
                variant="primary"
                size="sm"
                class="cursor-pointer shrink-0"
                @click="emit('openBatchModal')"
            >
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Generate Batch Stand &rarr;
            </BaseButton>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 bg-[#F8FAFC] p-3 rounded-[6px] border border-[#E2E8F0]">
            <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[240px]">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[180px]">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari stand / nama UMKM..."
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-1.5 text-xs text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                    />
                </div>

                <!-- Category Filter -->
                <select
                    v-model="categoryFilter"
                    class="rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-1.5 text-xs font-mono text-[#132952] focus:border-[#132952] focus:outline-none cursor-pointer"
                >
                    <option value="">Semua Kategori</option>
                    <option value="kuliner">Kuliner</option>
                    <option value="kriya">Kriya</option>
                    <option value="fesyen">Fesyen</option>
                    <option value="agribisnis">Agribisnis</option>
                    <option value="jasa">Jasa</option>
                </select>

                <!-- Status Filter -->
                <select
                    v-model="statusFilter"
                    class="rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-1.5 text-xs font-mono text-[#132952] focus:border-[#132952] focus:outline-none cursor-pointer"
                >
                    <option value="">Semua Status</option>
                    <option value="assigned">Terisi UMKM</option>
                    <option value="unassigned">Kosong / Tersedia</option>
                </select>
            </div>

            <div class="text-xs font-mono text-[#64748B]">
                Menampilkan <strong class="text-[#132952]">{{ filteredStands.length }}</strong> dari {{ stands.length }} stand
            </div>
        </div>

        <!-- Stand Cards Grid -->
        <div v-if="filteredStands.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="stand in filteredStands"
                :key="stand.id"
                class="bg-white border border-[#CBD5E1] hover:border-[#132952] rounded-[10px] p-4 shadow-xs transition-colors flex flex-col justify-between"
            >
                <div>
                    <!-- Top Bar: Stand Number & Status -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono text-sm font-bold text-[#132952] tracking-wider">
                            {{ stand.nomor_stand }}
                        </span>
                        <BaseBadge
                            :variant="stand.nama_usaha ? 'emerald' : 'navy'"
                            :dot="true"
                        >
                            {{ stand.nama_usaha ? 'TERISI' : 'KOSONG' }}
                        </BaseBadge>
                    </div>

                    <!-- UMKM Info or Empty State -->
                    <div class="min-h-[50px] mb-3">
                        <template v-if="stand.nama_usaha">
                            <h4 class="font-display font-semibold text-sm text-[#132952] truncate">
                                {{ stand.nama_usaha }}
                            </h4>
                            <p class="text-xs text-[#64748B] truncate">
                                Pemilik: {{ stand.nama_pemilik || '-' }}
                            </p>
                        </template>
                        <template v-else>
                            <p class="text-xs text-[#94A3B8] italic">
                                Belum terasosiasi pedagang. Bagikan link pendaftaran di bawah.
                            </p>
                        </template>
                    </div>

                    <!-- Specs: Category & Power -->
                    <div class="grid grid-cols-2 gap-2 py-2 border-y border-[#F1F5F9] text-xs font-mono mb-3">
                        <div>
                            <span class="text-[10px] text-[#64748B] uppercase block">Kategori</span>
                            <span class="font-semibold text-[#132952] uppercase">{{ stand.kategori_jualan }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-[#64748B] uppercase block">Daya Listrik</span>
                            <span class="font-semibold text-[#132952]">{{ stand.daya_listrik_watt }} W</span>
                        </div>
                    </div>

                    <!-- POS PIN Box -->
                    <div class="p-2.5 rounded-[6px] bg-[#F8FAFC] border border-[#CBD5E1] mb-3">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-mono text-[#64748B] uppercase block">PIN KASIR POS (6 DIGIT)</span>
                                <code class="text-sm font-mono font-bold text-[#132952] tracking-widest">
                                    {{ stand.pin_pos }}
                                </code>
                            </div>
                            <button
                                type="button"
                                class="px-2 py-1 rounded-[4px] border text-[11px] font-mono transition-colors cursor-pointer"
                                :class="copiedPinId === stand.id ? 'bg-[#00B262] text-white border-[#00B262]' : 'bg-white text-[#132952] border-[#CBD5E1] hover:border-[#132952]'"
                                @click="copyPin(stand)"
                            >
                                {{ copiedPinId === stand.id ? 'Tersalin! ✓' : 'Salin PIN' }}
                            </button>
                        </div>
                    </div>

                    <!-- Financial Performance -->
                    <div class="flex items-center justify-between text-xs font-mono text-[#64748B] mb-3">
                        <span>{{ stand.total_transaksi_terekam || 0 }} Transaksi</span>
                        <strong class="text-[#00B262]">
                            Rp {{ Number(stand.total_omzet_terekam || 0).toLocaleString('id-ID') }}
                        </strong>
                    </div>
                </div>

                <!-- Bottom Links / Actions -->
                <div class="pt-3 border-t border-[#F1F5F9] flex items-center justify-between gap-2">
                    <button
                        type="button"
                        class="text-xs font-mono text-[#5B6B8A] hover:text-[#132952] transition-colors cursor-pointer truncate"
                        @click="copyAssignLink(stand)"
                    >
                        <span v-if="copiedLinkId === stand.id" class="text-[#00B262] font-bold">Link Tersalin! ✓</span>
                        <span v-else>Salin Link Registrasi</span>
                    </button>

                    <a
                        :href="getWhatsAppShareUrl(stand)"
                        target="_blank"
                        class="inline-flex items-center gap-1 text-[11px] font-mono text-[#00B262] hover:underline shrink-0"
                    >
                        <span>Share WA</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="p-12 border border-dashed border-[#CBD5E1] rounded-[8px] text-center bg-[#F8FAFC]"
        >
            <svg class="h-10 w-10 mx-auto text-[#94A3B8] mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="font-mono text-sm text-[#64748B] font-semibold uppercase">Tidak Ada Stand Ditemukan</p>
            <p class="text-xs text-[#94A3B8] mt-1">Coba sesuaikan kata kunci pencarian atau generate slot stand baru.</p>
        </div>
    </div>
</template>
