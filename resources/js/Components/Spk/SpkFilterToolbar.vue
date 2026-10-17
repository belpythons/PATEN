<script setup>
const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            search: '',
            kecamatan: '',
            kategori: '',
            status: '',
            program: '',
        }),
    },
    totalCount: {
        type: Number,
        default: 0,
    },
    filteredCount: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['update:modelValue', 'reset']);

const kecamatanList = [
    'Sangatta Utara',
    'Sangatta Selatan',
    'Teluk Pandan',
    'Rantau Pulung',
    'Bengalon',
    'Kaliorang',
    'Sangkulirang',
    'Muara Wahau',
    'Kongbeng',
    'Telen',
    'Muara Ancalong',
    'Muara Bengkal',
    'Busang',
    'Batu Ampar',
    'Long Mesangat',
    'Karangan',
    'Kaubun',
    'Sandaran',
];

const updateField = (key, val) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [key]: val,
    });
};

const handleReset = () => {
    emit('reset');
};
</script>

<template>
    <div class="p-4 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs space-y-3">
        <!-- Row 1: Search & Filter Dropdowns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="relative">
                <input
                    :value="modelValue.search"
                    type="text"
                    placeholder="Cari Stand, Nama Usaha, Pemilik..."
                    class="w-full h-9 pl-8 pr-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] placeholder-[#94A3B8] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                    @input="updateField('search', $event.target.value)"
                />
                <svg class="h-4 w-4 text-[#94A3B8] absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filter Kecamatan (18 Kutim) -->
            <div>
                <select
                    :value="modelValue.kecamatan"
                    class="w-full h-9 px-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                    @change="updateField('kecamatan', $event.target.value)"
                >
                    <option value="">Semua Kecamatan (18 Kutim)</option>
                    <option v-for="kec in kecamatanList" :key="kec" :value="kec">
                        {{ kec }}
                    </option>
                </select>
            </div>

            <!-- Filter Kategori Usaha -->
            <div>
                <select
                    :value="modelValue.kategori"
                    class="w-full h-9 px-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                    @change="updateField('kategori', $event.target.value)"
                >
                    <option value="">Semua Kategori Jualan</option>
                    <option value="kuliner">Kuliner</option>
                    <option value="kriya">Kriya & Kerajinan</option>
                    <option value="fesyen">Fesyen & Batik</option>
                    <option value="agribisnis">Agribisnis / Pertanian</option>
                    <option value="jasa">Jasa Kreatif</option>
                </select>
            </div>

            <!-- Filter Status Kelayakan Guardrail -->
            <div>
                <select
                    :value="modelValue.status"
                    class="w-full h-9 px-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                    @change="updateField('status', $event.target.value)"
                >
                    <option value="">Semua Status Kelayakan</option>
                    <option value="prioritas">★ Prioritas Kuota 200 APBD</option>
                    <option value="lolos">Lolos Kepatuhan Perbup</option>
                    <option value="diskualifikasi">Diskualifikasi / Terkendala</option>
                </select>
            </div>

            <!-- Filter Program Rekomendasi -->
            <div>
                <select
                    :value="modelValue.program"
                    class="w-full h-9 px-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                    @change="updateField('program', $event.target.value)"
                >
                    <option value="">Semua Rekomendasi Bantuan</option>
                    <option value="hibah_modal_apbd">Hibah Modal APBD</option>
                    <option value="hibah_alat">Hibah Alat Usaha</option>
                    <option value="kredit_perbankan">Kredit BPR / KUR</option>
                    <option value="fasilitasi_legalitas">Fasilitasi Legalitas</option>
                </select>
            </div>
        </div>

        <!-- Row 2: Metadata & Quick Reset -->
        <div class="flex items-center justify-between text-xs font-mono text-[#64748B] pt-2 border-t border-[#F1F5F9]">
            <div class="flex items-center gap-2">
                <span>Menampilkan:</span>
                <span class="font-bold text-[#132952]">{{ filteredCount }}</span>
                <span>dari</span>
                <span class="font-bold text-[#132952]">{{ totalCount }}</span>
                <span>stand terdaftar</span>
            </div>

            <button
                v-if="modelValue.search || modelValue.kecamatan || modelValue.kategori || modelValue.status || modelValue.program"
                type="button"
                class="text-xs font-mono text-[#EF4444] hover:underline cursor-pointer"
                @click="handleReset"
            >
                Reset Semua Filter ✕
            </button>
        </div>
    </div>
</template>
