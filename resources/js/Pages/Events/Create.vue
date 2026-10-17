<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BaseInput from '@/Components/UI/BaseInput.vue';
import BaseSelect from '@/Components/UI/BaseSelect.vue';
import BaseTextarea from '@/Components/UI/BaseTextarea.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseToast from '@/Components/UI/BaseToast.vue';
import LiveThemeCard from '@/Components/Events/LiveThemeCard.vue';
import LockConfirmModal from '@/Components/Events/LockConfirmModal.vue';
import RundownRowBuilder from '@/Components/Events/RundownRowBuilder.vue';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const props = defineProps({
    defaultPrimaryColor: {
        type: String,
        default: '#132952',
    },
    defaultSecondaryColor: {
        type: String,
        default: '#00B262',
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
const isAuthGateModalOpen = ref(false);
const toastMessage = ref('');
const toastType = ref('error');

const colorPresets = [
    { name: 'Deep Navy Blue', hex: '#132952' },
    { name: 'Vibrant Emerald Green', hex: '#00B262' },
    { name: 'Warm Amber Orange', hex: '#FAA21B' },
];

const form = useForm({
    nama_event: '',
    deskripsi: '',
    penyelenggara: 'Dinas Koperasi & UKM Kabupaten Kutai Timur',
    lokasi_venue: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    color_mode: 'duo',
    color_primary: props.defaultPrimaryColor || '#132952',
    color_secondary: props.defaultSecondaryColor || '#00B262',
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

// Auto-fill rundown dates when event dates are set
watch(() => form.tanggal_mulai, (val) => {
    if (val && form.initial_rundowns[0] && !form.initial_rundowns[0].waktu_mulai) {
        form.initial_rundowns[0].waktu_mulai = val;
    }
});
watch(() => form.tanggal_selesai, (val) => {
    if (val && form.initial_rundowns[0] && !form.initial_rundowns[0].waktu_selesai) {
        form.initial_rundowns[0].waktu_selesai = val;
    }
});

const addRundownRow = () => {
    form.initial_rundowns.push({
        judul_sesi: '',
        waktu_mulai: form.tanggal_mulai || '',
        waktu_selesai: form.tanggal_selesai || '',
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
    // Basic field validation
    if (!form.nama_event || !form.penyelenggara || !form.lokasi_venue || !form.tanggal_mulai || !form.tanggal_selesai) {
        toastMessage.value = 'Mohon lengkapi seluruh kolom wajib bertanda bintang (*) sebelum melanjutkan.';
        toastType.value = 'error';
        window.scrollTo({ top: 120, behavior: 'smooth' });
        return;
    }

    // Workflow Gate: If user is not authenticated, prompt them to login first
    if (!authUser.value) {
        isAuthGateModalOpen.value = true;
        return;
    }

    isLockModalOpen.value = true;
};

const proceedWithoutAuth = () => {
    isAuthGateModalOpen.value = false;
    isLockModalOpen.value = true;
};

const submitFinalEvent = () => {
    // Clean up empty rundown rows so they don't cause 422
    const sanitizedRundowns = form.initial_rundowns.filter((r) => {
        return r.judul_sesi && r.judul_sesi.trim() !== '';
    }).map((r) => ({
        ...r,
        waktu_mulai: r.waktu_mulai || form.tanggal_mulai,
        waktu_selesai: r.waktu_selesai || form.tanggal_selesai,
    }));

    form.initial_rundowns = sanitizedRundowns;

    form.post('/events', {
        onSuccess: () => {
            isLockModalOpen.value = false;
        },
        onError: (errors) => {
            isLockModalOpen.value = false;
            const msgs = Object.values(errors).join(' ');
            toastMessage.value = msgs || 'Gagal menerbitkan event. Silakan periksa kembali input Anda.';
            toastType.value = 'error';
        },
    });
};
</script>

<template>
    <PublicLayout>
        <!-- Toast Notification for feedback -->
        <div v-if="toastMessage" class="fixed top-5 right-5 z-50 max-w-md">
            <BaseToast
                :message="toastMessage"
                :type="toastType"
                @dismiss="toastMessage = ''"
            />
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
            <!-- Header Section with Hallmark Triad Theme -->
            <div class="p-6 rounded-[10px] bg-gradient-to-r from-[#132952] to-[#1E3A6E] text-white shadow-md space-y-3">
                <div class="flex items-center gap-2 text-xs font-mono text-[#00B262]">
                    <span class="w-2 h-2 rounded-full bg-[#00B262] animate-pulse"></span>
                    <span>MODUL INISIALISASI EVENT RESMI KUTAI TIMUR</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-display font-bold text-white tracking-tight">
                    Inisialisasi Event Baru & Ekosistem Niaga
                </h1>
                <p class="text-xs sm:text-sm text-[#CBD5E1] max-w-3xl leading-relaxed">
                    Lengkapi parameter resmi acara untuk mempublikasikan portal publik warga, direktori stand bazar UMKM, kupon doorprize digital, dan menghubungkan event ke Dasbor Pengelola Anda.
                </p>
            </div>

            <!-- Workflow Authentication Notice Bar -->
            <div
                v-if="authUser"
                class="p-4 rounded-[8px] bg-[#00B262]/10 border border-[#00B262]/40 text-[#132952] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-xs"
            >
                <div class="flex items-center gap-2.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#00B262]"></span>
                    <div>
                        <span class="font-mono font-semibold text-[#00B262] uppercase">Terautentikasi:</span>
                        <strong class="ml-1 text-[#132952]">{{ authUser.name }}</strong>
                        <span class="text-[#64748B] ml-1">({{ authUser.role === 'admin_dinas' ? 'Dinas Koperasi UKM' : 'Panitia Pelaksana' }})</span>
                        <p class="text-[11px] text-[#5B6B8A]">Event yang Anda buat otomatis terhubung ke akun Anda di <strong>Dasbor Pengelola</strong>.</p>
                    </div>
                </div>
                <Link
                    href="/dashboard"
                    class="px-3 py-1.5 rounded-[4px] bg-[#132952] hover:bg-[#0D1E3D] text-white font-mono text-xs transition-colors shrink-0"
                >
                    Lihat Dasbor Saya &rarr;
                </Link>
            </div>

            <div
                v-else
                class="p-4 rounded-[8px] bg-[#FAA21B]/10 border border-[#FAA21B]/40 text-[#132952] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-xs"
            >
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">⚠️</span>
                    <div>
                        <span class="font-mono font-bold text-[#FAA21B] uppercase">PERINGATAN: ANDA BELUM MASUK AKUN PANITIA</span>
                        <p class="text-[11px] text-[#475569]">
                            Jika membuat event tanpa login, Anda tidak dapat melihat event ini di Dasbor nanti. Disarankan masuk terlebih dahulu.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <Link
                        href="/login"
                        class="px-3 py-1.5 rounded-[4px] bg-[#132952] hover:bg-[#0D1E3D] text-white font-mono text-xs font-semibold transition-colors"
                    >
                        Masuk (Login) &rarr;
                    </Link>
                    <Link
                        href="/register"
                        class="px-3 py-1.5 rounded-[4px] bg-white hover:bg-[#F8FAFC] text-[#132952] border border-[#CBD5E1] font-mono text-xs transition-colors"
                    >
                        Daftar Akun
                    </Link>
                </div>
            </div>

            <!-- Two-Column Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column (Form Controls) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Section 01: Identitas Acara -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="navy">01</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#132952]">
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
                            <BaseBadge variant="navy">02</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#132952]">
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

                    <!-- Section 03: Skema Warna & Tema -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="navy">03</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#132952]">
                                Skema Warna & Tema Visual
                            </h2>
                        </div>

                        <div class="space-y-4">
                            <!-- Color Mode Switch -->
                            <div>
                                <label class="block text-xs font-mono uppercase tracking-wider text-[#273A60] mb-1.5 font-medium">
                                    Mode Pewarnaan Halaman *
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button
                                        type="button"
                                        class="px-3 py-2 rounded-[6px] border text-xs font-mono transition-all text-center cursor-pointer"
                                        :class="form.color_mode === 'mono' ? 'border-[#132952] bg-[#132952] text-white font-bold' : 'border-[#CBD5E1] bg-white text-[#273A60] hover:border-[#132952]'"
                                        @click="form.color_mode = 'mono'"
                                    >
                                        Mono (Satu Warna)
                                    </button>
                                    <button
                                        type="button"
                                        class="px-3 py-2 rounded-[6px] border text-xs font-mono transition-all text-center cursor-pointer"
                                        :class="form.color_mode === 'duo' ? 'border-[#00B262] bg-[#00B262] text-white font-bold' : 'border-[#CBD5E1] bg-white text-[#273A60] hover:border-[#00B262]'"
                                        @click="form.color_mode = 'duo'"
                                    >
                                        Duo (Kombinasi Dua Warna)
                                    </button>
                                </div>
                            </div>

                            <!-- Color Presets -->
                            <div>
                                <label class="block text-xs font-mono uppercase tracking-wider text-[#273A60] mb-1.5 font-medium">
                                    Pilihan Warna Standar Triad PATEN
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="preset in colorPresets"
                                        :key="preset.hex"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-[6px] border text-xs font-mono transition-all cursor-pointer"
                                        :class="form.color_primary === preset.hex ? 'border-[#132952] bg-[#EDF2F9] text-[#132952] font-bold' : 'border-[#CBD5E1] bg-white hover:border-[#94A3B8] text-[#273A60]'"
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
                                    <label class="block text-xs font-mono uppercase tracking-wider text-[#273A60] mb-1.5 font-medium">
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
                                            placeholder="#132952"
                                            class="w-full rounded-[6px] border border-[#CBD5E1] px-3 py-2 text-xs font-mono uppercase text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none bg-white"
                                        />
                                    </div>
                                    <p v-if="form.errors.color_primary" class="mt-1 text-xs text-[#EF4444] font-mono">
                                        {{ form.errors.color_primary }}
                                    </p>
                                </div>

                                <div v-if="form.color_mode === 'duo'">
                                    <label class="block text-xs font-mono uppercase tracking-wider text-[#273A60] mb-1.5 font-medium">
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
                                            placeholder="#00B262"
                                            class="w-full rounded-[6px] border border-[#CBD5E1] px-3 py-2 text-xs font-mono uppercase text-[#132952] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none bg-white"
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
                            <BaseBadge variant="navy">04</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#132952]">
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
                                    <p class="font-medium text-xs sm:text-sm text-[#132952]">
                                        Klasemen Keaktifan Stand (Awarding UMKM)
                                    </p>
                                    <p class="text-xs text-[#5B6B8A]">
                                        Tampilkan peringkat stan teraktif di halaman publik dengan menyembunyikan nominal omzet.
                                    </p>
                                </div>
                                <input
                                    v-model="form.is_awarding_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-[#CBD5E1] text-[#132952] focus:ring-[#132952] cursor-pointer"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Section 05: Rundown Row Builder -->
                    <div class="rounded-[10px] border border-[#CBD5E1] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E2E8F0]">
                            <BaseBadge variant="navy">05</BaseBadge>
                            <h2 class="font-display font-semibold text-base text-[#132952]">
                                Rundown Jadwal Acara (Opsional)
                            </h2>
                        </div>

                        <p class="text-xs text-[#5B6B8A] mb-4">
                            Anda dapat menambahkan jadwal sesi sekarang atau mengelolanya nanti melalui CMS Pengelola.
                        </p>

                        <RundownRowBuilder
                            :rundowns="form.initial_rundowns"
                            @add="addRundownRow"
                            @remove="removeRundownRow"
                        />
                    </div>

                    <!-- CTA Submit Button -->
                    <div class="pt-4">
                        <BaseButton
                            variant="emerald"
                            size="lg"
                            class="w-full justify-center py-3 text-sm font-semibold tracking-wide shadow-sm"
                            :loading="form.processing"
                            @click="triggerConfirmModal"
                        >
                            Konfirmasi & Terbitkan Event Resmi &rarr;
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
                    <div class="rounded-[10px] border border-[#E2E8F0] bg-white p-5 text-xs space-y-2.5 text-[#273A60] shadow-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#00B262]"></span>
                            <p class="font-mono font-semibold uppercase text-[#132952] text-xs">
                                Standar Regulasi PATEN Kutim
                            </p>
                        </div>
                        <p class="leading-relaxed">
                            • <strong>Portal Publik Otomatis</strong>: Halaman warga terbit di <code class="font-mono text-[#00B262]">/e/{slug}</code> untuk jadwal dan tiket doorprize.
                        </p>
                        <p class="leading-relaxed">
                            • <strong>Terhubung ke Dasbor</strong>: Event otomatis tersimpan di Dasbor akun Anda untuk mengelola stand dan hasil seleksi hibah APBD.
                        </p>
                        <p class="leading-relaxed">
                            • <strong>Gerbang Kasir POS</strong>: Pedagang UMKM stand dapat langsung login dengan PIN 6-digit di <code class="font-mono text-[#132952]">/pos</code>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auth Gate Check Modal (Shown if user clicks submit without login) -->
        <BaseModal :show="isAuthGateModalOpen" max-width="md" @close="isAuthGateModalOpen = false">
            <template #header>
                <div class="flex items-center gap-2 text-[#FAA21B]">
                    <span class="text-lg">🔐</span>
                    <h3 class="font-display font-bold text-base text-[#132952]">
                        Masuk Akun Panitia Diperlukan
                    </h3>
                </div>
            </template>

            <div class="space-y-4 text-xs text-[#475569] leading-relaxed">
                <p>
                    Anda saat ini belum masuk ke akun Panitia. Agar event <strong>"{{ form.nama_event || 'Event Baru' }}"</strong> ini tersimpan di Dasbor dan dapat Anda kelola kembali sewaktu-waktu, silakan masuk ke akun Anda terlebih dahulu.
                </p>
                <div class="p-3 rounded-[6px] bg-[#00B262]/10 border border-[#00B262]/30 text-[#132952]">
                    💡 <strong>Tips</strong>: Anda dapat menggunakan akun demo <code>panitia@kutim.go.id</code> (Kata Sandi: <code>password123</code>) atau mendaftarkan akun panitia baru.
                </div>
            </div>

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 w-full">
                    <button
                        type="button"
                        @click="proceedWithoutAuth"
                        class="text-[11px] font-mono text-[#64748B] hover:text-[#132952] hover:underline"
                    >
                        Lanjut Tanpa Akun (Darurat)
                    </button>
                    <div class="flex items-center gap-2">
                        <Link
                            href="/login"
                            class="px-3 py-1.5 rounded-[4px] bg-[#132952] hover:bg-[#0D1E3D] text-white font-mono text-xs font-semibold transition-colors"
                        >
                            Masuk Sekarang &rarr;
                        </Link>
                        <Link
                            href="/register"
                            class="px-3 py-1.5 rounded-[4px] bg-white hover:bg-[#F8FAFC] text-[#132952] border border-[#CBD5E1] font-mono text-xs transition-colors"
                        >
                            Daftar Akun
                        </Link>
                    </div>
                </div>
            </template>
        </BaseModal>

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
