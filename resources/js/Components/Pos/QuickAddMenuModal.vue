<script setup>
import { useForm } from '@inertiajs/vue3';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseInput from '@/Components/UI/BaseInput.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    standId: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close', 'created']);

const form = useForm({
    nama_item: '',
    harga: '',
    is_available: true,
});

const setQuickPrice = (amount) => {
    form.harga = amount;
};

const submit = () => {
    form.post(`/pos/${props.standId}/menus`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('created');
            form.reset();
            emit('close');
        },
    });
};
</script>

<template>
    <BaseModal
        :show="show"
        title="Tambah Menu Kilat (POS)"
        max-width="md"
        @close="$emit('close')"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <BaseInput
                    v-model="form.nama_item"
                    label="Nama Menu / Produk Baru"
                    placeholder="Contoh: Es Kopi Susu Aren"
                    :error="form.errors.nama_item"
                    required
                    autofocus
                />
            </div>

            <div>
                <BaseInput
                    v-model.number="form.harga"
                    type="number"
                    min="0"
                    step="500"
                    label="Harga Jual Satuan (Rp)"
                    placeholder="Contoh: 15000"
                    :error="form.errors.harga"
                    required
                />

                <!-- Quick Price Presets -->
                <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                    <span class="text-[11px] font-mono text-[#64748B]">Preset Harga:</span>
                    <button
                        v-for="preset in [5000, 10000, 15000, 20000, 25000, 50000]"
                        :key="preset"
                        type="button"
                        class="px-2 py-0.5 rounded-[4px] bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[11px] font-mono text-[#132952] border border-[#CBD5E1] cursor-pointer"
                        @click="setQuickPrice(preset)"
                    >
                        {{ preset / 1000 }}rb
                    </button>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-[#E2E8F0]">
                <BaseButton
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="$emit('close')"
                >
                    Batal
                </BaseButton>
                <BaseButton
                    type="submit"
                    variant="emerald"
                    size="sm"
                    :loading="form.processing"
                >
                    + Simpan ke Katalog Stand
                </BaseButton>
            </div>
        </form>
    </BaseModal>
</template>
