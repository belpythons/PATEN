<script setup>
import { computed } from 'vue';

const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'openAddMenu']);

const selectedMenu = computed(() => {
    return props.menus.find(m => m.id === props.modelValue) || null;
});

const selectMenu = (menuId) => {
    emit('update:modelValue', menuId);
};
</script>

<template>
    <div class="space-y-3">
        <!-- Quick Pick Chips for Fast Tap -->
        <div v-if="menus.length > 0">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-mono uppercase tracking-wider font-semibold text-[#132952]">
                    Pilih Menu Cepat (1 Ketukan)
                </label>
                <button
                    type="button"
                    class="text-xs font-mono text-[#00B262] hover:text-[#009E57] font-semibold flex items-center gap-1 cursor-pointer"
                    @click="$emit('openAddMenu')"
                >
                    <span>+ Tambah Menu</span>
                </button>
            </div>

            <!-- Scrollable / wrapping chips -->
            <div class="flex flex-wrap gap-2 max-h-44 overflow-y-auto pr-1">
                <button
                    v-for="item in menus"
                    :key="item.id"
                    type="button"
                    class="px-3 py-2 rounded-[6px] border text-left transition-all duration-100 flex flex-col justify-between min-w-[130px] flex-1 sm:flex-initial cursor-pointer active:scale-95"
                    :class="[
                        modelValue === item.id
                            ? 'bg-[#00B262] text-white border-[#00B262] shadow-sm font-semibold ring-2 ring-[#00B262]/30'
                            : 'bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#132952] border-[#CBD5E1]'
                    ]"
                    @click="selectMenu(item.id)"
                >
                    <span class="text-xs font-sans font-medium truncate max-w-[150px]">{{ item.nama_item }}</span>
                    <span
                        class="text-[11px] font-mono mt-0.5"
                        :class="modelValue === item.id ? 'text-white' : 'text-[#64748B]'"
                    >
                        Rp {{ Number(item.harga).toLocaleString('id-ID') }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Fallback dropdown for dense list / accessibility -->
        <div v-if="menus.length > 6">
            <label class="block text-[11px] font-mono text-[#64748B] mb-1">
                Atau cari melalui daftar lengkap:
            </label>
            <select
                :value="modelValue"
                class="w-full h-10 px-3 rounded-[6px] bg-white border border-[#CBD5E1] text-[#132952] text-xs font-mono focus:outline-hidden focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                @change="selectMenu($event.target.value)"
            >
                <option value="" disabled>-- Pilih dari Semua Menu --</option>
                <option v-for="m in menus" :key="m.id" :value="m.id">
                    {{ m.nama_item }} — Rp {{ Number(m.harga).toLocaleString('id-ID') }}
                </option>
            </select>
        </div>

        <!-- Empty state when no menu exists yet -->
        <div v-if="menus.length === 0" class="p-4 rounded-[6px] bg-[#F8FAFC] border border-dashed border-[#CBD5E1] text-center">
            <p class="text-xs text-[#64748B] font-sans">Belum ada menu produk terdaftar pada stan ini.</p>
            <button
                type="button"
                class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] bg-[#00B262] text-white text-xs font-semibold hover:bg-[#009E57] cursor-pointer shadow-xs"
                @click="$emit('openAddMenu')"
            >
                + Daftarkan Menu Pertama
            </button>
        </div>
    </div>
</template>
