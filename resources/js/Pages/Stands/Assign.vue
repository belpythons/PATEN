<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import OfficialNoticeBanner from '@/Components/Pos/OfficialNoticeBanner.vue';
import BaseInput from '@/Components/UI/BaseInput.vue';
import BaseSelect from '@/Components/UI/BaseSelect.vue';
import BaseTextarea from '@/Components/UI/BaseTextarea.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseCard from '@/Components/UI/BaseCard.vue';

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
    disclaimer: {
        type: String,
        default: '',
    },
});

const kecamatanOptions = [
    { value: 'Sangatta Utara', label: 'Sangatta Utara' },
    { value: 'Sangatta Selatan', label: 'Sangatta Selatan' },
    { value: 'Teluk Pandan', label: 'Teluk Pandan' },
    { value: 'Rantau Pulung', label: 'Rantau Pulung' },
    { value: 'Bengalon', label: 'Bengalon' },
    { value: 'Kaliorang', label: 'Kaliorang' },
    { value: 'Sangkulirang', label: 'Sangkulirang' },
    { value: 'Muara Wahau', label: 'Muara Wahau' },
    { value: 'Kongbeng', label: 'Kongbeng' },
    { value: 'Telen', label: 'Telen' },
    { value: 'Muara Ancalong', label: 'Muara Ancalong' },
    { value: 'Muara Bengkal', label: 'Muara Bengkal' },
    { value: 'Busang', label: 'Busang' },
    { value: 'Batu Ampar', label: 'Batu Ampar' },
    { value: 'Long Mesangat', label: 'Long Mesangat' },
    { value: 'Karangan', label: 'Karangan' },
    { value: 'Kaubun', label: 'Kaubun' },
    { value: 'Sandaran', label: 'Sandaran' },
];

const initialMenus = ref(
    props.stand.menus && props.stand.menus.length
        ? props.stand.menus.map(m => ({ nama_item: m.nama_item, harga: m.harga }))
        : [
            { nama_item: '', harga: '' },
            { nama_item: '', harga: '' },
        ]
);

const addMenuItem = () => {
    initialMenus.value.push({ nama_item: '', harga: '' });
};

const removeMenuItem = (index) => {
    if (initialMenus.value.length > 1) {
        initialMenus.value.splice(index, 1);
    }
};

const form = useForm({
    nik: props.umkm?.nik || '',
    no_kk: props.umkm?.no_kk || '',
    nama_pemilik: props.umkm?.nama_pemilik || '',
    nama_usaha: props.umkm?.nama_usaha || '',
    nomor_wa: props.umkm?.nomor_wa || '',
    kecamatan: props.umkm?.kecamatan || 'Sangatta Utara',
    alamat_usaha: props.umkm?.alamat_usaha || '',
    nib: props.umkm?.nib || '',
    has_halal: props.umkm?.has_halal ?? false,
    has_pirt: props.umkm?.has_pirt ?? false,
    pernah_terima_hibah: props.umkm?.pernah_terima_hibah ?? false,
    tahun_terima_hibah: props.umkm?.tahun_terima_hibah || '',
    initial_menus: [],
});

const submit = () => {
    form.initial_menus = initialMenus.value.filter(m => m.nama_item && m.harga !== '');
    form.post(`/stands/${props.stand.id}/assign`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <PublicLayout>
        <Head :title="`Registrasi Stand ${stand.nomor_stand} — ${event.nama_event}`" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-6">
            <!-- Header Card -->
            <div class="p-5 sm:p-6 rounded-[10px] bg-white border border-[#CBD5E1] shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E2E8F0] pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <BaseBadge variant="navy" :dot="true">
                                {{ stand.nomor_stand }}
                            </BaseBadge>
                            <BaseBadge variant="emerald">
                                {{ stand.kategori_jualan.toUpperCase() }}
                            </BaseBadge>
                            <span class="text-xs font-mono text-[#64748B]">
                                Daya Listrik: {{ stand.daya_listrik_watt }} Watt
                            </span>
                        </div>
                        <h1 class="mt-2 text-xl sm:text-2xl font-bold font-display text-[#132952]">
                            Pendaftaran Stand & Profiling UMKM
                        </h1>
                        <p class="text-xs text-[#5B6B8A] mt-1 font-mono">
                            Event: <span class="font-semibold text-[#132952]">{{ event.nama_event }}</span> &middot; {{ event.lokasi_venue }}
                        </p>
                    </div>

                    <div class="text-left sm:text-right text-xs font-mono text-[#64748B]">
                        <p>Penyelenggara Resmi:</p>
                        <p class="font-bold text-[#132952]">{{ event.penyelenggara }}</p>
                    </div>
                </div>

                <!-- Anti-Tax Official Notice Banner (FR-3.2) -->
                <div class="mt-4">
                    <OfficialNoticeBanner :disclaimer="disclaimer" />
                </div>
            </div>

            <!-- Validation Errors Summary -->
            <div
                v-if="form.errors && Object.keys(form.errors).length > 0"
                class="p-4 rounded-[8px] bg-[#FEF2F2] border border-[#FCA5A5] text-[#991B1B]"
            >
                <div class="flex items-center gap-2 font-semibold text-xs">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Mohon periksa kembali formulir Anda:</span>
                </div>
                <ul class="mt-2 text-xs list-disc list-inside space-y-1 font-mono">
                    <li v-for="(msg, field) in form.errors" :key="field">
                        {{ msg }}
                    </li>
                </ul>
            </div>

            <!-- Main Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Section 1: Identitas & Verifikasi KK (Aturan 1 KK 1 Penerima) -->
                <BaseCard title="1. Identitas Pelaku Usaha (Kepatuhan Perbup Kutim)">
                    <p class="text-xs text-[#5B6B8A] mb-4">
                        Data kependudukan digunakan untuk kurasi bantuan modal APBD / BAZNAS sesuai aturan
                        <strong class="text-[#132952]">1 KK Maksimal 1 Penerima Hibah</strong>.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <BaseInput
                                v-model="form.nik"
                                label="Nomor Induk Kependudukan (NIK - 16 Digit)"
                                placeholder="Contoh: 6408041234567890"
                                maxlength="16"
                                :error="form.errors.nik"
                                required
                            />
                            <p class="mt-1 text-[11px] font-mono text-[#64748B] flex justify-between">
                                <span>16 Digit KTP</span>
                                <span :class="form.nik.length === 16 ? 'text-[#00B262] font-bold' : 'text-[#94A3B8]'">
                                    {{ form.nik.length }}/16
                                </span>
                            </p>
                        </div>

                        <div>
                            <BaseInput
                                v-model="form.no_kk"
                                label="Nomor Kartu Keluarga (No KK - 16 Digit)"
                                placeholder="Contoh: 6408049876543210"
                                maxlength="16"
                                :error="form.errors.no_kk"
                                required
                            />
                            <p class="mt-1 text-[11px] font-mono text-[#64748B] flex justify-between">
                                <span>Verifikasi 1 KK 1 Penerima</span>
                                <span :class="form.no_kk.length === 16 ? 'text-[#00B262] font-bold' : 'text-[#94A3B8]'">
                                    {{ form.no_kk.length }}/16
                                </span>
                            </p>
                        </div>

                        <div>
                            <BaseInput
                                v-model="form.nama_pemilik"
                                label="Nama Pemilik (Sesuai KTP)"
                                placeholder="Nama lengkap pemilik usaha"
                                :error="form.errors.nama_pemilik"
                                required
                            />
                        </div>

                        <div>
                            <BaseInput
                                v-model="form.nama_usaha"
                                label="Nama Usaha / Merk Dagang"
                                placeholder="Contoh: Kripik Singkong Sangatta Prima"
                                :error="form.errors.nama_usaha"
                                required
                            />
                        </div>

                        <div>
                            <BaseInput
                                v-model="form.nomor_wa"
                                label="Nomor WhatsApp Aktif"
                                placeholder="Contoh: 081234567890"
                                :error="form.errors.nomor_wa"
                                required
                            />
                        </div>

                        <div>
                            <BaseSelect
                                v-model="form.kecamatan"
                                label="Kecamatan Domisili Usaha (18 Kutim)"
                                :options="kecamatanOptions"
                                :error="form.errors.kecamatan"
                                required
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <BaseTextarea
                            v-model="form.alamat_usaha"
                            label="Alamat Lengkap Tempat Usaha / Produksi"
                            placeholder="Alamat jalan, nomor RT/RW, kelurahan atau desa di Kutai Timur"
                            rows="2"
                            :error="form.errors.alamat_usaha"
                        />
                    </div>
                </BaseCard>

                <!-- Section 2: Legalitas & Riwayat Bantuan -->
                <BaseCard title="2. Legalitas Usaha & Riwayat Hibah Pemerintah">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <BaseInput
                                v-model="form.nib"
                                label="Nomor Induk Berusaha (NIB)"
                                placeholder="Contoh: 1234567890123 (Kosongkan jika belum ada)"
                                :error="form.errors.nib"
                            />
                        </div>

                        <div class="flex flex-col justify-center space-y-2 pt-2">
                            <label class="text-xs font-mono uppercase tracking-wider font-semibold text-[#132952]">
                                Sertifikasi Produk
                            </label>
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-[#132952]">
                                    <input
                                        v-model="form.has_halal"
                                        type="checkbox"
                                        class="h-4 w-4 rounded-[4px] border-[#CBD5E1] text-[#00B262] focus:ring-[#00B262]"
                                    />
                                    <span>Sertifikat Halal</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer text-xs text-[#132952]">
                                    <input
                                        v-model="form.has_pirt"
                                        type="checkbox"
                                        class="h-4 w-4 rounded-[4px] border-[#CBD5E1] text-[#00B262] focus:ring-[#00B262]"
                                    />
                                    <span>Izin P-IRT</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#E2E8F0]">
                        <div class="flex items-start sm:items-center justify-between gap-4 flex-col sm:flex-row">
                            <div>
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <input
                                        v-model="form.pernah_terima_hibah"
                                        type="checkbox"
                                        class="h-4 w-4 rounded-[4px] border-[#CBD5E1] text-[#132952] focus:ring-[#132952]"
                                    />
                                    <span class="text-xs font-semibold text-[#132952]">
                                        Pernah Menerima Program Bantuan/Hibah Modal Pemkab Kutim Sebelumnya
                                    </span>
                                </label>
                                <p class="text-[11px] text-[#64748B] mt-0.5 ml-6.5">
                                    Centang jika pelaku usaha pernah menerima bantuan modal peralatan, BAZNAS, atau APBD Kutim.
                                </p>
                            </div>

                            <div v-if="form.pernah_terima_hibah" class="w-full sm:w-48">
                                <BaseInput
                                    v-model.number="form.tahun_terima_hibah"
                                    type="number"
                                    min="2010"
                                    :max="new Date().getFullYear()"
                                    label="Tahun Terima Hibah"
                                    placeholder="Contoh: 2024"
                                    :error="form.errors.tahun_terima_hibah"
                                />
                            </div>
                        </div>
                    </div>
                </BaseCard>

                <!-- Section 3: Katalog Menu Awal Stand -->
                <BaseCard title="3. Katalog Menu Awal Stand (Produk Utama Acara)">
                    <p class="text-xs text-[#5B6B8A] mb-3">
                        Daftarkan minimal 1 menu produk yang dijual pada stan ini. Menu ini akan langsung muncul di terminal kasir. Anda tetap dapat menambah atau mengubah menu nanti di terminal kasir.
                    </p>

                    <div class="space-y-3">
                        <div
                            v-for="(item, index) in initialMenus"
                            :key="index"
                            class="p-3 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1] flex flex-col sm:flex-row items-center gap-3"
                        >
                            <span class="text-xs font-mono font-bold text-[#64748B] w-8">
                                #{{ index + 1 }}
                            </span>

                            <div class="flex-1 w-full sm:w-auto">
                                <input
                                    v-model="item.nama_item"
                                    type="text"
                                    placeholder="Nama Menu / Produk (cth: Es Teh Jumbo)"
                                    class="w-full h-9 px-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-sans text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                                />
                            </div>

                            <div class="w-full sm:w-44">
                                <div class="relative">
                                    <span class="absolute left-2.5 top-2 text-xs font-mono text-[#94A3B8]">Rp</span>
                                    <input
                                        v-model.number="item.harga"
                                        type="number"
                                        min="0"
                                        step="500"
                                        placeholder="10000"
                                        class="w-full h-9 pl-9 pr-3 rounded-[6px] border border-[#CBD5E1] bg-white text-xs font-mono text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-end w-full sm:w-auto">
                                <button
                                    v-if="initialMenus.length > 1"
                                    type="button"
                                    class="p-1.5 rounded-[4px] text-[#EF4444] hover:bg-[#FEE2E2] transition-colors cursor-pointer"
                                    title="Hapus baris menu"
                                    @click="removeMenuItem(index)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] border border-[#CBD5E1] bg-white hover:bg-[#F1F5F9] text-xs font-mono text-[#132952] cursor-pointer"
                            @click="addMenuItem"
                        >
                            <span>+ Tambah Baris Menu</span>
                        </button>
                    </div>
                </BaseCard>

                <!-- Submit Bar -->
                <div class="p-4 rounded-[10px] bg-white border border-[#CBD5E1] flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                    <div class="text-xs text-[#5B6B8A] font-mono">
                        Dengan menekan simpan, UMKM dikaitkan resmi ke {{ stand.nomor_stand }}.
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <BaseButton
                            type="submit"
                            variant="emerald"
                            size="lg"
                            :loading="form.processing"
                            class="w-full sm:w-auto"
                        >
                            Simpan & Buka Terminal Kasir →
                        </BaseButton>
                    </div>
                </div>
            </form>
        </div>
    </PublicLayout>
</template>
