<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    defaultPrimaryColor: {
        type: String,
        default: '#0F766E'
    },
    defaultSecondaryColor: {
        type: String,
        default: '#F59E0B'
    },
    gamifikasiOptions: {
        type: Array,
        default: () => []
    }
});

const isConfirmModalOpen = ref(false);

const form = useForm({
    nama_event: '',
    deskripsi: '',
    penyelenggara: 'Dinas Koperasi & UKM Kabupaten Kutai Timur',
    lokasi_venue: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    color_mode: 'duo',
    color_primary: props.defaultPrimaryColor,
    color_secondary: props.defaultSecondaryColor,
    gamifikasi_mode: 'doorprize',
    harga_htm: 0,
    is_awarding_active: true,
    initial_rundowns: [
        {
            judul_sesi: 'Pembukaan & Registrasi Stand UMKM',
            waktu_mulai: '',
            waktu_selesai: '',
            pengisi_acara: 'Panitia Pelaksana',
            deskripsi_sesi: 'Persiapan awal stan pameran'
        }
    ]
});

const addRundownRow = () => {
    form.initial_rundowns.push({
        judul_sesi: '',
        waktu_mulai: '',
        waktu_selesai: '',
        pengisi_acara: '',
        deskripsi_sesi: ''
    });
};

const removeRundownRow = (index) => {
    if (form.initial_rundowns.length > 1) {
        form.initial_rundowns.splice(index, 1);
    }
};

const openConfirmModal = () => {
    isConfirmModalOpen.value = true;
};

const closeConfirmModal = () => {
    isConfirmModalOpen.value = false;
};

const submitFinalEvent = () => {
    form.post('/events', {
        onSuccess: () => {
            isConfirmModalOpen.value = false;
        }
    });
};
</script>

<template>
    <div>
        <h1>PATEN - Formulir Pembuatan Event Baru</h1>
        <p>Inisialisasi pameran, konfigurasi tema warna, opsi gamifikasi, dan jadwal kegiatan awal.</p>

        <div v-if="Object.keys(form.errors).length > 0">
            <h3>Kesalahan Validasi:</h3>
            <ul>
                <li v-for="(error, key) in form.errors" :key="key">
                    {{ error }}
                </li>
            </ul>
        </div>

        <form @submit.prevent="openConfirmModal">
            <fieldset>
                <legend>Informasi Utama Event</legend>

                <div>
                    <label for="nama_event">Nama Event:</label><br>
                    <input id="nama_event" v-model="form.nama_event" type="text" required>
                </div>

                <div>
                    <label for="penyelenggara">Instansi / Penyelenggara:</label><br>
                    <input id="penyelenggara" v-model="form.penyelenggara" type="text" required>
                </div>

                <div>
                    <label for="lokasi_venue">Lokasi Venue:</label><br>
                    <input id="lokasi_venue" v-model="form.lokasi_venue" type="text" required>
                </div>

                <div>
                    <label for="deskripsi">Deskripsi Acara:</label><br>
                    <textarea id="deskripsi" v-model="form.deskripsi" rows="3" required></textarea>
                </div>

                <div>
                    <label for="tanggal_mulai">Tanggal & Waktu Mulai:</label><br>
                    <input id="tanggal_mulai" v-model="form.tanggal_mulai" type="datetime-local" required>
                </div>

                <div>
                    <label for="tanggal_selesai">Tanggal & Waktu Selesai:</label><br>
                    <input id="tanggal_selesai" v-model="form.tanggal_selesai" type="datetime-local" required>
                </div>
            </fieldset>

            <fieldset>
                <legend>Konfigurasi Tema Desain & Warna</legend>

                <div>
                    <label for="color_mode">Mode Skema Warna:</label><br>
                    <select id="color_mode" v-model="form.color_mode">
                        <option value="mono">Opsi 1 Warna (Mono - Primary)</option>
                        <option value="duo">Opsi 2 Warna (Duo - Primary & Secondary)</option>
                    </select>
                </div>

                <div>
                    <label for="color_primary">Warna Utama (Primary Hex):</label><br>
                    <input id="color_primary" v-model="form.color_primary" type="color">
                    <span>{{ form.color_primary }}</span>
                </div>

                <div v-if="form.color_mode === 'duo'">
                    <label for="color_secondary">Warna Sekunder (Secondary Hex):</label><br>
                    <input id="color_secondary" v-model="form.color_secondary" type="color">
                    <span>{{ form.color_secondary }}</span>
                </div>
            </fieldset>

            <fieldset>
                <legend>Opsi Gamifikasi & Awarding</legend>

                <div>
                    <label for="gamifikasi_mode">Mode Kehadiran Pengunjung:</label><br>
                    <select id="gamifikasi_mode" v-model="form.gamifikasi_mode">
                        <option v-for="opt in gamifikasiOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                </div>

                <div v-if="['htm', 'both'].includes(form.gamifikasi_mode)">
                    <label for="harga_htm">Harga Tiket HTM (Rp):</label><br>
                    <input id="harga_htm" v-model.number="form.harga_htm" type="number" min="0" step="1000">
                </div>

                <div>
                    <label>
                        <input v-model="form.is_awarding_active" type="checkbox">
                        Aktifkan Mode Awarding UMKM Teraktif (Leaderboard Publik)
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Jadwal Rundown Awal</legend>

                <div v-for="(item, index) in form.initial_rundowns" :key="index">
                    <h4>Sesi {{ index + 1 }}</h4>
                    <label>Judul Sesi:</label><br>
                    <input v-model="item.judul_sesi" type="text" required><br>

                    <label>Waktu Mulai:</label><br>
                    <input v-model="item.waktu_mulai" type="datetime-local" required><br>

                    <label>Waktu Selesai:</label><br>
                    <input v-model="item.waktu_selesai" type="datetime-local" required><br>

                    <label>Pengisi Acara:</label><br>
                    <input v-model="item.pengisi_acara" type="text"><br>

                    <button type="button" @click="removeRundownRow(index)">Hapus Sesi Ini</button>
                    <hr>
                </div>

                <button type="button" @click="addRundownRow">+ Tambah Sesi Rundown</button>
            </fieldset>

            <br>
            <button type="submit">Tinjau & Konfirmasi Pembuatan Event</button>
        </form>

        <!-- Modal Konfirmasi Lock (Raw Functional HTML) -->
        <div v-if="isConfirmModalOpen">
            <hr>
            <h2>Konfirmasi & Kunci Informasi Event</h2>
            <p>Periksa kembali ringkasan berikut sebelum data disimpan dan token CMS di-generate:</p>

            <table>
                <tbody>
                    <tr>
                        <th>Nama Event:</th>
                        <td>{{ form.nama_event }}</td>
                    </tr>
                    <tr>
                        <th>Penyelenggara:</th>
                        <td>{{ form.penyelenggara }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi:</th>
                        <td>{{ form.lokasi_venue }}</td>
                    </tr>
                    <tr>
                        <th>Waktu:</th>
                        <td>{{ form.tanggal_mulai }} s/d {{ form.tanggal_selesai }}</td>
                    </tr>
                    <tr>
                        <th>Mode Warna:</th>
                        <td>{{ form.color_mode }} ({{ form.color_primary }} / {{ form.color_secondary }})</td>
                    </tr>
                    <tr>
                        <th>Gamifikasi:</th>
                        <td>{{ form.gamifikasi_mode }}</td>
                    </tr>
                    <tr>
                        <th>Total Rundown Awal:</th>
                        <td>{{ form.initial_rundowns.length }} sesi</td>
                    </tr>
                </tbody>
            </table>

            <br>
            <button type="button" @click="closeConfirmModal">Kembali Edit</button>
            <button type="button" :disabled="form.processing" @click="submitFinalEvent">
                {{ form.processing ? 'Menyimpan...' : 'Kunci & Buat Event Sekarang' }}
            </button>
            <hr>
        </div>
    </div>
</template>
