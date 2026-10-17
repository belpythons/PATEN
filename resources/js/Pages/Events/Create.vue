<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BaseInput from '@/Components/UI/BaseInput.vue';
import BaseSelect from '@/Components/UI/BaseSelect.vue';
import BaseTextarea from '@/Components/UI/BaseTextarea.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import LiveThemeCard from '@/Components/Events/LiveThemeCard.vue';
import LockConfirmModal from '@/Components/Events/LockConfirmModal.vue';
import RundownRowBuilder from '@/Components/Events/RundownRowBuilder.vue';

const props = defineProps({
    defaultPrimaryColor: {
        type: String,
        default: '#2563EB',
    },
    defaultSecondaryColor: {
        type: String,
        default: '#F59E0B',
    },
    gamifikasiOptions: {
        type: Array,
        default: () => [
            { value: 'doorprize', label: 'Mode B: Absensi Kupon Doorprize (Event Gratis)' },
            { value: 'htm', label: 'Mode A: Tiket HTM Berbayar' },
            { value: 'both', label: 'Kombinasi HTM & Doorprize' },
            { value: 'none', label: 'Tanpa Tiket & Doorprize' },
        ],
    },
});

const isLockModalOpen = ref(false);

const colorPresets = [
    { name: 'Cobalt', hex: '#2563EB' },
    { name: 'Kutim Teal', hex: '#0F766E' },
    { name: 'Amber Gold', hex: '#F59E0B' },
    { name: 'Obsidian', hex: '#1E293B' },
    { name: 'Emerald', hex: '#10B981' },
];

const form = useForm({
    nama_event: '',
    deskripsi: '',
    penyelenggara: 'Dinas Koperasi & UKM Kabupaten Kutai Timur',
    lokasi_venue: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    color_mode: 'duo',
    color_primary: props.defaultPrimaryColor || '#2563EB',
    color_secondary: props.defaultSecondaryColor || '#F59E0B',
    gamifikasi_mode: 'doorprize',
    harga_htm: 0,
    is_awarding_active: true,
    initial_rundowns: [
        {
            judul_sesi: 'Pembukaan & Registrasi Stand UMKM',
            waktu_mulai: '',
            waktu_selesai: '',
            pengisi_acara: 'Panitia Pelaksana',
            deskripsi_sesi: 'Persiapan awal stan pameran',
        },
    ],
});

const addRundownRow = () => {
    form.initial_rundowns.push({
        judul_sesi: '',
        waktu_mulai: '',
        waktu_selesai: '',
        pengisi_acara: '',
        deskripsi_sesi: '',
    });
};

const removeRundownRow = (index) => {
    if (form.initial_rundowns.length > 1) {
        form.initial_rundowns.splice(index, 1);
    }
};

const triggerConfirmModal = () => {
    if (!form.nama_event || !form.penyelenggara || !form.lokasi_venue || !form.tanggal_mulai || !form.tanggal_selesai) {
        alert('Mohon lengkapi data wajib acara sebelum melanjutkan.');
        return;
    }
    isLockModalOpen.value = true;
};

const submitFinalEvent = () => {
    form.post('/events', {
        onSuccess: () => {
            isLockModalOpen.value = false;
        },
        onError: () => {
            isLockModalOpen.value = false;
        },
    });
};
</script>

<template>
    <PublicLayout>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <!-- Asymmetric Header -->
            <div class="mb-8 pb-6 border-b border-[#E2E8F0]">
                <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#2563EB] font-semibold mb-2">
                    01 / INISIALISASI INFRASTRUKTUR EVENT
                </p>
                <h1 class="text-2xl sm:text-4xl font-display font-bold text-[#0F172A] tracking-tight">
                    Inisialisasi Event Baru & Ekosistem Niaga
                </h1>
                <p class="mt-2 text-sm text-[#475569] max-w-2xl">
                    Lengkapi parameter resmi acara untuk men-generate portal microsite publik, token CMS rahasia 64-karakter, lembar poster ber-QR, dan pembagian kupon doorprize digital.
                </p>
            </div>

            <!-- Two-Column Asymmetric Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column (Form Wizard Kontrol) -->
                <div class="lg:col-span-7 space-y-8">
                    <!-- Section 01: Identitas Acara -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="cobalt">01</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#0F172A]">
                                Identitas Resmi Acara
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <BaseInput
                                id="nama_event"
                                v-model="form.nama_event"
                                label="Nama Event / Pameran *"
                                placeholder="Contoh: Pekan Raya Kutai Timur 2026"
                                :required="true"
                                :error="form.errors.nama_event"
                            />

                            <BaseInput
                                id="penyelenggara"
                                v-model="form.penyelenggara"
                                label="Instansi Penyelenggara *"
                                placeholder="Contoh: Dinas Koperasi & UKM Kabupaten Kutai Timur"
                                :required="true"
                                :error="form.errors.penyelenggara"
                            />

                            <BaseTextarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                label="Deskripsi Acara *"
                                placeholder="Jelaskan tujuan event, target peserta UMKM, dan rangkaian festival daerah..."
                                :rows="3"
                                :required="true"
                                :error="form.errors.deskripsi"
                            />
                        </div>
                    </div>

                    <!-- Section 02: Waktu & Lokasi Venue -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="cobalt">02</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#0F172A]">
                                Waktu & Lokasi Venue
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <BaseInput
                                id="lokasi_venue"
                                v-model="form.lokasi_venue"
                                label="Lokasi Venue Acara *"
                                placeholder="Contoh: Kawasan Bukit Pelangi, Sangatta Utara"
                                :required="true"
                                :error="form.errors.lokasi_venue"
                            />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <BaseInput
                                    id="tanggal_mulai"
                                    v-model="form.tanggal_mulai"
                                    type="datetime-local"
                                    label="Tanggal & Waktu Mulai *"
                                    :required="true"
                                    :error="form.errors.tanggal_mulai"
                                />

                                <BaseInput
                                    id="tanggal_selesai"
                                    v-model="form.tanggal_selesai"
                                    type="datetime-local"
                                    label="Tanggal & Waktu Selesai *"
                                    :required="true"
                                    :error="form.errors.tanggal_selesai"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Section 03: Custom Design System (Color Mode) -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="cobalt">03</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#0F172A]">
                                Skema Warna & Tema Tampilan
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <!-- Mono vs Duo Selector -->
                            <div>
                                <label class="block text-xs font-mono uppercase tracking-wider text-[#334155] mb-2 font-medium">
                                    Mode Palet Warna
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button
                                        type="button"
                                        class="rounded-[6px] border p-3 text-left transition-all cursor-pointer"
                                        :class="form.color_mode === 'mono' ? 'border-[#2563EB] bg-[#EFF6FF] text-[#2563EB]' : 'border-[#CBD5E1] bg-white hover:border-[#94A3B8]'"
                                        @click="form.color_mode = 'mono'"
                                    >
                                        <p class="font-display font-semibold text-xs sm:text-sm">Opsi 1: Mode Mono</p>
                                        <p class="text-[11px] text-[#64748B] mt-0.5">Satu warna primer solid dominan</p>
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-[6px] border p-3 text-left transition-all cursor-pointer"
                                        :class="form.color_mode === 'duo' ? 'border-[#2563EB] bg-[#EFF6FF] text-[#2563EB]' : 'border-[#CBD5E1] bg-white hover:border-[#94A3B8]'"
                                        @click="form.color_mode = 'duo'"
                                    >
                                        <p class="font-display font-semibold text-xs sm:text-sm">Opsi 2: Mode Duo</p>
                                        <p class="text-[11px] text-[#64748B] mt-0.5">Kombinasi warna primer & sekunder</p>
                                    </button>
                                </div>
                            </div>

                            <!-- Color Presets Swatches -->
                            <div>
                                <label class="block text-[11px] font-mono text-[#475569] mb-1.5">
                                    Pilih Cepat Palet Warna Resmi
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="preset in colorPresets"
                                        :key="preset.hex"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] border text-xs font-mono transition-all cursor-pointer"
                                        :class="form.color_primary === preset.hex ? 'border-[#2563EB] bg-[#EFF6FF] text-[#2563EB] font-bold' : 'border-[#CBD5E1] bg-white hover:border-[#94A3B8] text-[#334155]'"
                                        @click="form.color_primary = preset.hex"
                                    >
                                        <span class="h-3 w-3 rounded-full border border-black/10 shrink-0" :style="{ backgroundColor: preset.hex }"></span>
                                        <span>{{ preset.name }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Manual Hex Inputs -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-mono uppercase tracking-wider text-[#334155] mb-1.5 font-medium">
                                        Warna Primer (Hex) *
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="form.color_primary"
                                            type="color"
                                            class="h-9 w-10 p-0.5 rounded-[6px] border border-[#CBD5E1] cursor-pointer bg-white"
                                        />
                                        <input
                                            v-model="form.color_primary"
                                            type="text"
                                            placeholder="#2563EB"
                                            class="w-full rounded-[6px] border border-[#CBD5E1] px-3 py-2 text-xs font-mono uppercase text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none bg-white"
                                        />
                                    </div>
                                    <p v-if="form.errors.color_primary" class="mt-1 text-xs text-[#EF4444] font-mono">
                                        {{ form.errors.color_primary }}
                                    </p>
                                </div>

                                <div v-if="form.color_mode === 'duo'">
                                    <label class="block text-xs font-mono uppercase tracking-wider text-[#334155] mb-1.5 font-medium">
                                        Warna Sekunder (Hex)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="form.color_secondary"
                                            type="color"
                                            class="h-9 w-10 p-0.5 rounded-[6px] border border-[#CBD5E1] cursor-pointer bg-white"
                                        />
                                        <input
                                            v-model="form.color_secondary"
                                            type="text"
                                            placeholder="#F59E0B"
                                            class="w-full rounded-[6px] border border-[#CBD5E1] px-3 py-2 text-xs font-mono uppercase text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none bg-white"
                                        />
                                    </div>
                                    <p v-if="form.errors.color_secondary" class="mt-1 text-xs text-[#EF4444] font-mono">
                                        {{ form.errors.color_secondary }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 04: Gamifikasi Publik -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="cobalt">04</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#0F172A]">
                                Konfigurasi Gamifikasi & Tiket
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <BaseSelect
                                id="gamifikasi_mode"
                                v-model="form.gamifikasi_mode"
                                label="Opsi Gamifikasi Kehadiran Pengunjung *"
                                :options="gamifikasiOptions"
                                :error="form.errors.gamifikasi_mode"
                            />

                            <!-- HTM Price input -->
                            <div v-if="form.gamifikasi_mode === 'htm' || form.gamifikasi_mode === 'both'">
                                <BaseInput
                                    id="harga_htm"
                                    v-model="form.harga_htm"
                                    type="number"
                                    label="Nominal Tiket HTM (Rupiah) *"
                                    placeholder="Contoh: 15000"
                                    :error="form.errors.harga_htm"
                                />
                            </div>

                            <!-- Awarding Switch -->
                            <div class="pt-2 border-t border-[#E2E8F0] flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-xs sm:text-sm text-[#0F172A]">
                                        Klasemen Keaktifan Stand (Awarding UMKM)
                                    </p>
                                    <p class="text-xs text-[#64748B]">
                                        Tampilkan peringkat stan teraktif di halaman publik dengan menyembunyikan nominal rupiah omzet.
                                    </p>
                                </div>
                                <input
                                    v-model="form.is_awarding_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-[#CBD5E1] text-[#2563EB] focus:ring-[#2563EB] cursor-pointer"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Section 05: Rundown Row Builder -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="cobalt">05</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#0F172A]">
                                Rundown Jadwal Pembukaan
                            </h2>
                        </div>

                        <RundownRowBuilder
                            :rundowns="form.initial_rundowns"
                            @add="addRundownRow"
                            @remove="removeRundownRow"
                        />
                    </div>

                    <!-- CTA Submit Button -->
                    <div class="pt-4">
                        <BaseButton
                            variant="primary"
                            size="lg"
                            class="w-full"
                            @click="triggerConfirmModal"
                        >
                            Lanjut ke Konfirmasi Penguncian & Penerbitan Token &rarr;
                        </BaseButton>
                    </div>
                </div>

                <!-- Right Column (Sticky Live Theme Mockup Card) -->
                <div class="lg:col-span-5 sticky top-20 space-y-6">
                    <LiveThemeCard
                        :nama-event="form.nama_event"
                        :penyelenggara="form.penyelenggara"
                        :lokasi-venue="form.lokasi_venue"
                        :color-mode="form.color_mode"
                        :color-primary="form.color_primary"
                        :color-secondary="form.color_secondary"
                        :gamifikasi-mode="form.gamifikasi_mode"
                        :harga-htm="form.harga_htm"
                    />

                    <!-- Helper Guidelines -->
                    <div class="rounded-[10px] border border-[#E2E8F0] bg-white p-4 text-xs space-y-2 text-[#475569]">
                        <p class="font-mono font-semibold uppercase text-[#0F172A] text-[11px]">
                            Standar Regulasi Sistem PATEN
                        </p>
                        <p>
                            • URL publik halaman acara akan otomatis dibuat berdasarkan nama event: <code class="font-mono text-[#2563EB]">/e/{slug}</code>.
                        </p>
                        <p>
                            • Token pengelolaan CMS diterbitkan secara kriptografis 64-karakter untuk akses panitia tanpa login kata sandi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lock Confirmation Modal -->
        <LockConfirmModal
            :show="isLockModalOpen"
            :form="form"
            :is-submitting="form.processing"
            @close="isLockModalOpen = false"
            @confirm="submitFinalEvent"
        />
    </PublicLayout>
</template>
