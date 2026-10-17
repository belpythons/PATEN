<script setup>
import { ref } from 'vue';

const props = defineProps({
    disclaimer: {
        type: String,
        default: 'Pencatatan transaksi ini adalah portofolio verifikasi kurasi bantuan UMKM Kutai Timur (Hibah Modal APBD, BAZNAS, Bantuan Alat Usaha), BUKAN untuk dasar penarikan pajak daerah atau penambahan biaya sewa stand.',
    },
    collapsible: {
        type: Boolean,
        default: false,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const isCollapsed = ref(false);

const toggleCollapse = () => {
    if (props.collapsible) {
        isCollapsed.value = !isCollapsed.value;
    }
};
</script>

<template>
    <aside
        aria-label="Pemberitahuan Resmi Pemerintah Kabupaten Kutai Timur"
        class="rounded-[8px] border border-[#FAA21B]/40 bg-[#FFFBEB] text-[#78350F] p-3.5 sm:p-4 shadow-xs transition-all relative overflow-hidden"
        :class="{ 'py-2.5': compact }"
    >
        <!-- Decorative subtle left accent border line -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#FAA21B]"></div>

        <div class="pl-2">
            <!-- Header Bar -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <!-- Official Shield Icon -->
                    <div class="h-6 w-6 rounded-[4px] bg-[#FAA21B]/20 text-[#B45309] flex items-center justify-center shrink-0">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-[3px] bg-[#FAA21B] text-white">
                            RESMI PEMKAB KUTAI TIMUR
                        </span>
                        <span class="text-xs font-semibold text-[#92400E]">
                            Dinas Koperasi & UKM Kab. Kutai Timur
                        </span>
                    </div>
                </div>

                <div v-if="collapsible" class="flex items-center">
                    <button
                        type="button"
                        class="text-[11px] font-mono text-[#B45309] hover:underline cursor-pointer"
                        @click="toggleCollapse"
                    >
                        {{ isCollapsed ? 'Lihat Detail ▼' : 'Tutup ▲' }}
                    </button>
                </div>
            </div>

            <!-- Body Text -->
            <div v-show="!isCollapsed" class="mt-2 text-xs leading-relaxed text-[#78350F]">
                <p>
                    <strong class="font-semibold text-[#92400E]">Anti-Pajak & Garansi Portofolio Hibah (FR-3.2):</strong>
                    {{ disclaimer }}
                </p>
                <div class="mt-1.5 flex items-center gap-2 text-[11px] font-mono text-[#B45309]">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#00B262]"></span>
                    <span>Kepatuhan Perbup Kutim: Penyaluran Hibah & Bantuan Alat Usaha Mikro</span>
                </div>
            </div>
        </div>
    </aside>
</template>
