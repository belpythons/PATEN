<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
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

const emit = defineEmits(['close', 'success']);

const form = useForm({
    jumlah_stand: 5,
    kategori_jualan: 'kuliner',
    daya_listrik_watt: 450,
    fasilitas_detail: {
        meja: 1,
        kursi: 2,
        tenda: true,
        stop_kontak: true,
        air_bersih: false,
    },
});

const quickQuantities = [5, 10, 20, 25];

const setQuantity = (qty) => {
    form.jumlah_stand = qty;
};

const submitForm = () => {
    form.post(`/cms/${props.event.slug}/stands?token=${props.token}`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('success', `${form.jumlah_stand} slot stand berhasil digenerate!`);
            emit('close');
            form.reset();
        },
    });
};
</script>

<template>
    <BaseModal
        :show="show"
        title="Batch Provisioning Stand & PIN Kasir"
        max-width="md"
        @close="emit('close')"
    >
        <form class="space-y-4" @submit.prevent="submitForm">
            <!-- Notice -->
            <div class="p-3 rounded-[6px] bg-[#EDF2F9] border border-[#CBD5E1] text-[11px] font-mono text-[#132952] leading-relaxed">
                <span class="font-bold">SISTEM GENERATOR OTOMATIS:</span>
                Setiap slot stand yang dibuat akan otomatis diterbitkan dengan <strong>PIN Kasir 6-digit unik</strong> dan tautan WhatsApp profiling UMKM.
            </div>

            <!-- Jumlah Stand -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-mono text-[#475569]">Jumlah Stand Dibuat *</label>
                    <div class="flex items-center gap-1">
                        <button
                            v-for="q in quickQuantities"
                            :key="q"
                            type="button"
                            class="px-2 py-0.5 rounded-[4px] border text-[10px] font-mono transition-colors cursor-pointer"
                            :class="form.jumlah_stand === q ? 'bg-[#132952] text-white border-[#132952]' : 'bg-[#F8FAFC] text-[#64748B] border-[#CBD5E1] hover:border-[#132952]'"
                            @click="setQuantity(q)"
                        >
                            +{{ q }}
                        </button>
                    </div>
                </div>
                <input
                    v-model.number="form.jumlah_stand"
                    type="number"
                    min="1"
                    max="100"
                    required
                    class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-2 text-xs font-mono font-bold text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                />
            </div>

            <!-- Kategori Stand -->
            <div>
                <label class="block text-xs font-mono text-[#475569] mb-1">Kategori Usaha Default *</label>
                <select
                    v-model="form.kategori_jualan"
                    class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-2 text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none cursor-pointer"
                >
                    <option value="kuliner">Kuliner (Makanan & Minuman)</option>
                    <option value="kriya">Kriya & Kerajinan Tangan</option>
                    <option value="fesyen">Fesyen & Busana Adat</option>
                    <option value="agribisnis">Agribisnis & Olahan Tani</option>
                    <option value="jasa">Jasa & Produk Kreatif</option>
                </select>
            </div>

            <!-- Daya Listrik -->
            <div>
                <label class="block text-xs font-mono text-[#475569] mb-1">Kapasitas Daya Listrik (Watt) *</label>
                <div class="grid grid-cols-4 gap-2 mb-2">
                    <button
                        v-for="watt in [0, 450, 900, 1300]"
                        :key="watt"
                        type="button"
                        class="py-1.5 px-2 rounded-[6px] border text-center text-xs font-mono transition-colors cursor-pointer"
                        :class="form.daya_listrik_watt === watt ? 'bg-[#132952] text-white border-[#132952] font-bold' : 'bg-[#F8FAFC] text-[#475569] border-[#CBD5E1] hover:border-[#132952]'"
                        @click="form.daya_listrik_watt = watt"
                    >
                        {{ watt === 0 ? 'Non-Listrik' : watt + ' W' }}
                    </button>
                </div>
                <input
                    v-model.number="form.daya_listrik_watt"
                    type="number"
                    min="0"
                    step="50"
                    required
                    class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-1.5 text-xs font-mono text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                    placeholder="Atau ketik watt manual (misal: 2200)"
                />
            </div>

            <!-- Checklist Fasilitas Lapangan -->
            <div class="pt-2 border-t border-[#E2E8F0]">
                <label class="block text-xs font-mono text-[#475569] mb-2 font-semibold">Fasilitas Stand Disediakan</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="flex items-center gap-2 text-[#334155] cursor-pointer">
                        <input v-model="form.fasilitas_detail.tenda" type="checkbox" class="rounded text-[#132952] focus:ring-[#132952]" />
                        <span>Tenda Sarnafil</span>
                    </label>
                    <label class="flex items-center gap-2 text-[#334155] cursor-pointer">
                        <input v-model="form.fasilitas_detail.stop_kontak" type="checkbox" class="rounded text-[#132952] focus:ring-[#132952]" />
                        <span>Stop Kontak Listrik</span>
                    </label>
                    <label class="flex items-center gap-2 text-[#334155] cursor-pointer">
                        <input v-model="form.fasilitas_detail.air_bersih" type="checkbox" class="rounded text-[#132952] focus:ring-[#132952]" />
                        <span>Akses Air Bersih</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-[#64748B] text-[11px] font-mono">Meja:</span>
                        <input v-model.number="form.fasilitas_detail.meja" type="number" min="0" class="w-12 px-1.5 py-0.5 border border-[#CBD5E1] rounded text-xs font-mono" />
                        <span class="text-[#64748B] text-[11px] font-mono">Kursi:</span>
                        <input v-model.number="form.fasilitas_detail.kursi" type="number" min="0" class="w-12 px-1.5 py-0.5 border border-[#CBD5E1] rounded text-xs font-mono" />
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#CBD5E1]">
                <BaseButton
                    type="button"
                    variant="secondary"
                    size="sm"
                    @click="emit('close')"
                >
                    Batal
                </BaseButton>
                <BaseButton
                    type="submit"
                    variant="primary"
                    size="sm"
                    :loading="form.processing"
                >
                    Generate {{ form.jumlah_stand }} Stand &rarr;
                </BaseButton>
            </div>
        </form>
    </BaseModal>
</template>
