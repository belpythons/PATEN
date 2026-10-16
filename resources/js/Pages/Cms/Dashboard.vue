<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    token: String,
    metrics: Object,
    rundowns: Array,
    stands: Array,
    recent_attendees: Array,
    doorprize_winners: Array,
    public_url: String,
    cms_url: String,
    errors: Object,
});

// Flash messages & feedback
const statusMessage = ref('');
const errorMessage = ref('');

// Form for Adding Rundown
const rundownForm = useForm({
    judul_sesi: '',
    waktu_mulai: '',
    waktu_selesai: '',
    pengisi_acara: '',
    urutan: props.rundowns ? props.rundowns.length + 1 : 1,
});

const submitRundown = () => {
    statusMessage.value = '';
    errorMessage.value = '';
    rundownForm.post(`/cms/${props.event.slug}/rundowns?token=${props.token}`, {
        onSuccess: () => {
            statusMessage.value = 'Sesi rundown berhasil ditambahkan!';
            rundownForm.reset('judul_sesi', 'waktu_mulai', 'waktu_selesai', 'pengisi_acara');
            rundownForm.urutan = props.rundowns.length + 1;
        },
        onError: (errs) => {
            errorMessage.value = 'Gagal menambah rundown: ' + Object.values(errs).join(', ');
        },
    });
};

const deleteRundown = (id) => {
    if (!confirm('Yakin ingin menghapus sesi rundown ini?')) return;
    statusMessage.value = '';
    errorMessage.value = '';
    router.delete(`/cms/${props.event.slug}/rundowns/${id}?token=${props.token}`, {
        onSuccess: () => {
            statusMessage.value = 'Sesi rundown berhasil dihapus!';
        },
    });
};

// Form for Stand Provisioning
const standForm = useForm({
    jumlah_stand: 5,
    kategori_jualan: 'kuliner',
    daya_listrik_watt: 450,
});

const submitStandProvision = () => {
    statusMessage.value = '';
    errorMessage.value = '';
    standForm.post(`/cms/${props.event.slug}/stands?token=${props.token}`, {
        onSuccess: () => {
            statusMessage.value = `${standForm.jumlah_stand} Stand baru berhasil digenerate beserta PIN kasir!`;
        },
        onError: (errs) => {
            errorMessage.value = 'Gagal generate stand: ' + Object.values(errs).join(', ');
        },
    });
};

// Form for Gate Check-in
const checkInTicketNumber = ref('');
const checkInLoading = ref(false);
const checkInResult = ref(null);

const submitCheckIn = async () => {
    statusMessage.value = '';
    errorMessage.value = '';
    checkInResult.value = null;
    if (!checkInTicketNumber.value) {
        errorMessage.value = 'Nomor tiket wajib diisi.';
        return;
    }

    checkInLoading.value = true;
    try {
        const response = await fetch(`/cms/${props.event.slug}/check-in?token=${props.token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CMS-Token': props.token,
            },
            body: JSON.stringify({
                nomor_tiket_doorprize: checkInTicketNumber.value,
            }),
        });
        const data = await response.json();
        if (response.ok && data.success) {
            checkInResult.value = data.attendee;
            statusMessage.value = data.message;
            checkInTicketNumber.value = '';
            // Refresh metrics via Inertia reload
            router.reload({ only: ['metrics', 'recent_attendees'] });
        } else {
            errorMessage.value = data.message || 'Check-in gagal.';
        }
    } catch (err) {
        errorMessage.value = 'Terjadi kesalahan sistem saat check-in.';
    } finally {
        checkInLoading.value = false;
    }
};

// Form for Doorprize Drawing
const drawCount = ref(1);
const drawLoading = ref(false);
const drawWinnersResult = ref([]);

const submitDrawDoorprize = async () => {
    statusMessage.value = '';
    errorMessage.value = '';
    drawLoading.value = true;
    try {
        const response = await fetch(`/cms/${props.event.slug}/draw-doorprize?token=${props.token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CMS-Token': props.token,
            },
            body: JSON.stringify({
                jumlah_pemenang: drawCount.value,
            }),
        });
        const data = await response.json();
        if (response.ok && data.success) {
            drawWinnersResult.value = data.winners;
            statusMessage.value = data.message;
            router.reload({ only: ['doorprize_winners'] });
        } else {
            errorMessage.value = data.message || 'Undian doorprize gagal.';
        }
    } catch (err) {
        errorMessage.value = 'Terjadi kesalahan koneksi sistem saat mengundi doorprize.';
    } finally {
        drawLoading.value = false;
    }
};
</script>

<template>
    <div>
        <h1>CMS Pengelola Event - PATEN Kutai Timur</h1>
        <p><strong>Nama Event:</strong> {{ event.nama_event }}</p>
        <p><strong>Penyelenggara:</strong> {{ event.penyelenggara }} | <strong>Lokasi:</strong> {{ event.lokasi_venue }}</p>
        <p><strong>URL Publik:</strong> <a :href="public_url" target="_blank">{{ public_url }}</a></p>
        <p><strong>Hidden CMS URL:</strong> <code>{{ cms_url }}</code></p>
        <p><strong>Secret Token (64 char):</strong> <code>{{ event.secure_management_token }}</code></p>

        <hr />

        <div v-if="statusMessage">
            <p><strong>[STATUS SUKSES]:</strong> {{ statusMessage }}</p>
        </div>
        <div v-if="errorMessage">
            <p><strong>[STATUS ERROR]:</strong> {{ errorMessage }}</p>
        </div>

        <hr />

        <h2>Ringkasan Metrik Acara</h2>
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Total Stand Aktif</th>
                    <th>Total Pengunjung Terdaftar</th>
                    <th>Total Check-in di Gate</th>
                    <th>Total Omzet Terekam</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ metrics.total_stands }} Stand</td>
                    <td>{{ metrics.total_attendees }} Orang</td>
                    <td>{{ metrics.checked_in_attendees }} Hadir</td>
                    <td>Rp {{ Number(metrics.total_omzet).toLocaleString('id-ID') }}</td>
                </tr>
            </tbody>
        </table>

        <hr />

        <h2>1. Manajemen Rundown Lapangan</h2>
        <form @submit.prevent="submitRundown">
            <p>
                <label>Judul Sesi: </label>
                <input v-model="rundownForm.judul_sesi" type="text" placeholder="Contoh: Sambutan Bupati" required />
            </p>
            <p>
                <label>Waktu Mulai: </label>
                <input v-model="rundownForm.waktu_mulai" type="datetime-local" required />
                <label> Waktu Selesai: </label>
                <input v-model="rundownForm.waktu_selesai" type="datetime-local" required />
            </p>
            <p>
                <label>Pengisi Acara: </label>
                <input v-model="rundownForm.pengisi_acara" type="text" placeholder="Nama Narasumber / MC" />
                <label> Urutan: </label>
                <input v-model.number="rundownForm.urutan" type="number" min="1" />
            </p>
            <button type="submit" :disabled="rundownForm.processing">Tambah Sesi Rundown</button>
        </form>

        <h3>Daftar Sesi Rundown Saat Ini</h3>
        <table border="1" cellpadding="4" cellspacing="0" v-if="rundowns && rundowns.length">
            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Judul Sesi</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Pengisi Acara</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="r in rundowns" :key="r.id">
                    <td>{{ r.urutan }}</td>
                    <td>{{ r.judul_sesi }}</td>
                    <td>{{ r.waktu_mulai }}</td>
                    <td>{{ r.waktu_selesai }}</td>
                    <td>{{ r.pengisi_acara || '-' }}</td>
                    <td>
                        <button type="button" @click="deleteRundown(r.id)">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <p v-else>Belum ada jadwal rundown yang terdaftar.</p>

        <hr />

        <h2>2. Generator Stand & PIN Kasir POS</h2>
        <form @submit.prevent="submitStandProvision">
            <p>
                <label>Jumlah Stand: </label>
                <input v-model.number="standForm.jumlah_stand" type="number" min="1" max="100" required />
            </p>
            <p>
                <label>Kategori Jualan: </label>
                <select v-model="standForm.kategori_jualan">
                    <option value="kuliner">Kuliner</option>
                    <option value="kriya">Kriya</option>
                    <option value="fesyen">Fesyen</option>
                    <option value="agribisnis">Agribisnis</option>
                    <option value="jasa">Jasa</option>
                </select>
            </p>
            <p>
                <label>Daya Listrik (Watt): </label>
                <input v-model.number="standForm.daya_listrik_watt" type="number" min="0" step="50" required />
            </p>
            <button type="submit" :disabled="standForm.processing">Generate Stand & PIN Kasir</button>
        </form>

        <h3>Daftar Stand Terdaftar (Kredensial Kasir)</h3>
        <table border="1" cellpadding="4" cellspacing="0" v-if="stands && stands.length">
            <thead>
                <tr>
                    <th>Nomor Stand</th>
                    <th>Kategori</th>
                    <th>Daya (Watt)</th>
                    <th>PIN Kasir (6 Digit)</th>
                    <th>Total Transaksi</th>
                    <th>Total Omzet</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="s in stands" :key="s.id">
                    <td><strong>{{ s.nomor_stand }}</strong></td>
                    <td>{{ s.kategori_jualan }}</td>
                    <td>{{ s.daya_listrik_watt }} W</td>
                    <td><code>{{ s.pin_pos }}</code></td>
                    <td>{{ s.total_transaksi_terekam || 0 }}</td>
                    <td>Rp {{ Number(s.total_omzet_terekam || 0).toLocaleString('id-ID') }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else>Belum ada stand yang digenerate.</p>

        <hr />

        <h2>3. Operasional Gate Check-in</h2>
        <form @submit.prevent="submitCheckIn">
            <p>
                <label>Nomor Tiket / Kupon: </label>
                <input v-model="checkInTicketNumber" type="text" placeholder="Contoh: PATEN-DOOR-20261017-ABCD" required />
                <button type="submit" :disabled="checkInLoading">Check-in Pengunjung</button>
            </p>
        </form>

        <div v-if="checkInResult">
            <h3>Hasil Check-in Terakhir:</h3>
            <p><strong>Nama:</strong> {{ checkInResult.nama_lengkap }}</p>
            <p><strong>Nomor Tiket:</strong> {{ checkInResult.nomor_tiket_doorprize }}</p>
            <p><strong>Tipe Registrasi:</strong> {{ checkInResult.tipe_registrasi }}</p>
            <p><strong>Waktu Check-in:</strong> {{ checkInResult.check_in_time }}</p>
        </div>

        <hr />

        <h2>4. Undian Pemenang Doorprize Acak</h2>
        <form @submit.prevent="submitDrawDoorprize">
            <p>
                <label>Jumlah Pemenang: </label>
                <input v-model.number="drawCount" type="number" min="1" max="10" required />
                <button type="submit" :disabled="drawLoading">Undi Pemenang Acak</button>
            </p>
        </form>

        <div v-if="drawWinnersResult && drawWinnersResult.length">
            <h3>Pemenang Terpilih Baru Saja:</h3>
            <ul>
                <li v-for="w in drawWinnersResult" :key="w.id">
                    <strong>{{ w.nama_lengkap }}</strong> - Tiket: <code>{{ w.nomor_tiket_doorprize }}</code> ({{ w.kecamatan }})
                </li>
            </ul>
        </div>

        <h3>Daftar Seluruh Pemenang Doorprize Event Ini</h3>
        <table border="1" cellpadding="4" cellspacing="0" v-if="doorprize_winners && doorprize_winners.length">
            <thead>
                <tr>
                    <th>Nama Pemenang</th>
                    <th>Nomor Tiket</th>
                    <th>Kecamatan</th>
                    <th>Waktu Hadir Check-in</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="w in doorprize_winners" :key="w.id">
                    <td>{{ w.nama_lengkap }}</td>
                    <td><code>{{ w.nomor_tiket_doorprize }}</code></td>
                    <td>{{ w.kecamatan }}</td>
                    <td>{{ w.check_in_time }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else>Belum ada pemenang doorprize yang diundi.</p>
    </div>
</template>
