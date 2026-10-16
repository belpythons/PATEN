<script setup>
import { ref } from 'vue';

const props = defineProps({
    event: {
        type: Object,
        required: true
    },
    rundowns: {
        type: Array,
        default: () => []
    },
    qrCodeUrl: {
        type: String,
        default: ''
    }
});

const form = ref({
    nama_lengkap: '',
    nomor_wa: '',
    kecamatan: 'Sangatta Utara',
    tipe_registrasi: props.event.gamifikasi_mode === 'htm' ? 'htm' : 'doorprize'
});

const isSubmitting = ref(false);
const registrationResult = ref(null);
const errorMessage = ref('');

const registerAttendee = async () => {
    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch(`/e/${props.event.slug}/register-attendance`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(form.value)
        });

        const data = await response.json();

        if (response.ok) {
            registrationResult.value = data.ticket;
            form.value.nama_lengkap = '';
            form.value.nomor_wa = '';
        } else {
            errorMessage.value = data.message || 'Terjadi kesalahan saat pendaftaran.';
        }
    } catch (err) {
        errorMessage.value = 'Gagal menghubungi server.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div>
        <h1>{{ event.nama_event }}</h1>
        <p>Diselenggarakan oleh: {{ event.penyelenggara }}</p>
        <p>Lokasi: {{ event.lokasi_venue }}</p>
        <p>Waktu: {{ event.tanggal_mulai }} s/d {{ event.tanggal_selesai }}</p>

        <div v-if="qrCodeUrl">
            <h3>QR Code Resmi Event (Scan untuk Membuka Halaman Ini):</h3>
            <img :src="qrCodeUrl" alt="QR Code Halaman Event">
        </div>

        <hr>

        <h2>Jadwal Rundown Acara</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Sesi</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Pengisi Acara / Narasumber</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in rundowns" :key="item.id || index">
                    <td>{{ item.urutan || (index + 1) }}</td>
                    <td>{{ item.judul_sesi }}</td>
                    <td>{{ item.waktu_mulai }}</td>
                    <td>{{ item.waktu_selesai }}</td>
                    <td>{{ item.pengisi_acara || '-' }}</td>
                </tr>
                <tr v-if="rundowns.length === 0">
                    <td colspan="5">Belum ada rundown yang dipublikasikan.</td>
                </tr>
            </tbody>
        </table>

        <hr>

        <!-- Formulir Pendaftaran Gamifikasi Pengunjung -->
        <div v-if="event.gamifikasi_mode !== 'none'">
            <h2>Pendaftaran Kehadiran Pengunjung & E-Tiket / Kupon</h2>
            <p v-if="event.gamifikasi_mode === 'doorprize'">
                Daftarkan diri Anda untuk mendapatkan nomor undian Doorprize digital resmi Gebyar UMKM Kutim!
            </p>
            <p v-else-if="event.gamifikasi_mode === 'htm'">
                Pendaftaran Tiket Masuk (HTM): Rp {{ event.harga_htm }}
            </p>

            <div v-if="errorMessage" style="color: red;">
                <p><strong>Peringatan:</strong> {{ errorMessage }}</p>
            </div>

            <!-- Tampilan Tiket Setelah Berhasil -->
            <div v-if="registrationResult">
                <h3>Selamat! Pendaftaran Anda Berhasil Terdata</h3>
                <table>
                    <tbody>
                        <tr>
                            <th>Nomor Kupon / Tiket:</th>
                            <td><strong>{{ registrationResult.nomor_tiket_doorprize }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap:</th>
                            <td>{{ registrationResult.nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Pendaftaran:</th>
                            <td>{{ registrationResult.tipe_registrasi }}</td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="registrationResult.qr_data_url">
                    <p>Tunjukkan QR Code berikut di meja gate masuk acara:</p>
                    <img :src="registrationResult.qr_data_url" alt="QR Code Tiket">
                </div>
                <hr>
            </div>

            <form @submit.prevent="registerAttendee">
                <div>
                    <label for="nama_lengkap">Nama Lengkap:</label><br>
                    <input id="nama_lengkap" v-model="form.nama_lengkap" type="text" required>
                </div>

                <div>
                    <label for="nomor_wa">Nomor WhatsApp:</label><br>
                    <input id="nomor_wa" v-model="form.nomor_wa" type="tel" placeholder="08xxxxxxxxxx" required>
                </div>

                <div>
                    <label for="kecamatan">Kecamatan Asal:</label><br>
                    <select id="kecamatan" v-model="form.kecamatan">
                        <option value="Sangatta Utara">Sangatta Utara</option>
                        <option value="Sangatta Selatan">Sangatta Selatan</option>
                        <option value="Bengalon">Bengalon</option>
                        <option value="Teluk Pandan">Teluk Pandan</option>
                        <option value="Rantau Pulung">Rantau Pulung</option>
                        <option value="Muara Wahau">Muara Wahau</option>
                        <option value="Kongbeng">Kongbeng</option>
                        <option value="Sangkulirang">Sangkulirang</option>
                    </select>
                </div>

                <div v-if="event.gamifikasi_mode === 'both'">
                    <label for="tipe_registrasi">Pilihan Kehadiran:</label><br>
                    <select id="tipe_registrasi" v-model="form.tipe_registrasi">
                        <option value="doorprize">Kupon Doorprize Gratis</option>
                        <option value="htm">Tiket HTM Masuk</option>
                    </select>
                </div>

                <br>
                <button type="submit" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Memproses...' : 'Daftarkan Kehadiran & Ambil Tiket/Kupon' }}
                </button>
            </form>
        </div>
    </div>
</template>
