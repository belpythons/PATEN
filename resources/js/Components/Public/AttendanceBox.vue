<script setup>
import { ref, computed } from 'vue';
import BaseInput from '@/Components/UI/BaseInput.vue';
import BaseSelect from '@/Components/UI/BaseSelect.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['registered']);

// 18 Kecamatan di Kabupaten Kutai Timur
const KECAMATAN_KUTIM = [
    { value: 'Sangatta Utara', label: 'Sangatta Utara' },
    { value: 'Sangatta Selatan', label: 'Sangatta Selatan' },
    { value: 'Bengalon', label: 'Bengalon' },
    { value: 'Teluk Pandan', label: 'Teluk Pandan' },
    { value: 'Rantau Pulung', label: 'Rantau Pulung' },
    { value: 'Muara Wahau', label: 'Muara Wahau' },
    { value: 'Kongbeng', label: 'Kongbeng' },
    { value: 'Sangkulirang', label: 'Sangkulirang' },
    { value: 'Kaliorang', label: 'Kaliorang' },
    { value: 'Kaubun', label: 'Kaubun' },
    { value: 'Karangan', label: 'Karangan' },
    { value: 'Sandaran', label: 'Sandaran' },
    { value: 'Muara Bengkal', label: 'Muara Bengkal' },
    { value: 'Muara Ancalong', label: 'Muara Ancalong' },
    { value: 'Busang', label: 'Busang' },
    { value: 'Long Mesangat', label: 'Long Mesangat' },
    { value: 'Batu Ampar', label: 'Batu Ampar' },
    { value: 'Telen', label: 'Telen' },
];

const initialTipeRegistrasi = computed(() => {
    if (props.event.gamifikasi_mode === 'htm') return 'htm';
    return 'doorprize';
});

const form = ref({
    nama_lengkap: '',
    nomor_wa: '',
    kecamatan: 'Sangatta Utara',
    tipe_registrasi: initialTipeRegistrasi.value,
});

const isSubmitting = ref(false);
const errorMessage = ref('');

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID').format(val || 0);
};

const registerAttendee = async () => {
    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch(`/e/${props.event.slug}/register-attendance`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form.value),
        });

        const data = await response.json();

        if (response.ok) {
            emit('registered', data.ticket);
            // Reset fields
            form.value.nama_lengkap = '';
            form.value.nomor_wa = '';
        } else {
            errorMessage.value = data.message || 'Terjadi kesalahan saat pendaftaran. Pastikan data terisi dengan benar.';
        }
    } catch (err) {
        errorMessage.value = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <section id="registrasi-tiket" class="scroll-mt-20">
        <div class="bg-white border border-[#E2E8F0] rounded-[10px] p-6 sm:p-8 shadow-xs">
            <div class="max-w-xl">
                <!-- Section Header -->
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#2563EB] font-semibold">
                        03 / REGISTRASI KEHADIRAN PENGUNJUNG
                    </span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-display font-bold text-[#0F172A] tracking-tight">
                    Ambil E-Kupon & Tiket Masuk
                </h2>
                <p class="text-sm text-[#64748B] mt-1.5 leading-relaxed">
                    <template v-if="event.gamifikasi_mode === 'doorprize'">
                        Daftarkan kehadiran Anda untuk memperoleh nomor kupon Doorprize digital resmi Gebyar UMKM Kutai Timur yang diundi langsung di panggung.
                    </template>
                    <template v-else-if="event.gamifikasi_mode === 'htm'">
                        Pendaftaran Tiket Masuk (HTM): <strong class="text-[#0F172A]">Rp {{ formatRupiah(event.harga_htm) }}</strong>. Dapatkan barcode tiket resmi untuk akses gate masuk acara.
                    </template>
                    <template v-else>
                        Registrasi kehadiran resmi pengunjung untuk verifikasi gate dan akses fasilitas pameran bazar UMKM.
                    </template>
                </p>

                <!-- Error Alert -->
                <div
                    v-if="errorMessage"
                    class="mt-4 p-3.5 bg-[#FEF2F2] border border-[#FECACA] rounded-[6px] text-xs text-[#991B1B] flex items-start gap-2.5"
                    role="alert"
                >
                    <svg class="w-4 h-4 text-[#EF4444] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-semibold">Pemberitahuan:</span> {{ errorMessage }}
                    </div>
                </div>

                <!-- Registration Form -->
                <form class="mt-6 space-y-4" @submit.prevent="registerAttendee">
                    <BaseInput
                        id="nama_lengkap"
                        v-model="form.nama_lengkap"
                        label="Nama Lengkap Sesuai Identitas"
                        placeholder="Contoh: Ahmad Fauzi"
                        required
                    />

                    <BaseInput
                        id="nomor_wa"
                        v-model="form.nomor_wa"
                        label="Nomor WhatsApp Aktif"
                        type="tel"
                        placeholder="08xxxxxxxxxx"
                        hint="Digunakan untuk validasi pemenang undian doorprize atau konfirmasi tiket."
                        required
                    />

                    <BaseSelect
                        id="kecamatan"
                        v-model="form.kecamatan"
                        label="Kecamatan Domisili di Kutai Timur"
                        :options="KECAMATAN_KUTIM"
                        hint="Data domisili dipakai untuk pemetaan statistik persebaran pengunjung daerah."
                        required
                    />

                    <div v-if="event.gamifikasi_mode === 'both'" class="space-y-1">
                        <label class="block text-xs font-mono uppercase tracking-wider text-[#334155] font-medium">
                            Jenis Registrasi
                        </label>
                        <BaseSelect
                            id="tipe_registrasi"
                            v-model="form.tipe_registrasi"
                            :options="[
                                { value: 'doorprize', label: 'Kupon Doorprize Gratis' },
                                { value: 'htm', label: `Tiket Masuk HTM (Rp ${formatRupiah(event.harga_htm)})` }
                            ]"
                        />
                    </div>

                    <div class="pt-2">
                        <BaseButton
                            type="submit"
                            variant="primary"
                            size="md"
                            :loading="isSubmitting"
                            class="w-full sm:w-auto"
                        >
                            {{ isSubmitting ? 'Menerbitkan Tiket / Kupon...' : 'Daftarkan Kehadiran & Ambil Tiket/Kupon' }}
                        </BaseButton>
                    </div>

                    <p class="text-[11px] font-mono text-[#94A3B8] pt-2">
                        * Pendaftaran dibatasi untuk keamanan sistem & validitas kupon doorprize digital.
                    </p>
                </form>
            </div>
        </div>
    </section>
</template>
