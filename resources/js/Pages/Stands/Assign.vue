<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    stand: Object,
    event: Object,
    umkm: Object,
    disclaimer: String,
});

const initialMenus = ref([
    { nama_item: '', harga: '' },
]);

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
    has_halal: props.umkm?.has_halal || false,
    has_pirt: props.umkm?.has_pirt || false,
    pernah_terima_hibah: props.umkm?.pernah_terima_hibah || false,
    tahun_terima_hibah: props.umkm?.tahun_terima_hibah || '',
    initial_menus: initialMenus.value,
});

const submit = () => {
    form.initial_menus = initialMenus.value.filter(m => m.nama_item && m.harga);
    form.post(`/stands/${props.stand.id}/assign`);
};
</script>

<template>
    <div>
        <h1>Registrasi & Asosiasi UMKM Stand - {{ stand.nomor_stand }}</h1>
        <p><strong>Event:</strong> {{ event.nama_event }} ({{ event.penyelenggara }})</p>
        <p><strong>Lokasi:</strong> {{ event.lokasi_venue }}</p>
        <p><strong>Kategori Jualan:</strong> {{ stand.kategori_jualan }} | <strong>Daya Listrik:</strong> {{ stand.daya_listrik_watt }} W</p>

        <hr />

        <div style="border: 2px solid black; padding: 10px; margin: 10px 0;">
            <p><strong>[PEMBERITAHUAN RESMI PEMKAB KUTAI TIMUR]:</strong></p>
            <p>{{ disclaimer }}</p>
        </div>

        <hr />

        <div v-if="form.errors && Object.keys(form.errors).length">
            <p><strong>Terdapat kesalahan pengisian data:</strong></p>
            <ul>
                <li v-for="(err, field) in form.errors" :key="field">{{ err }}</li>
            </ul>
            <hr />
        </div>

        <form @submit.prevent="submit">
            <h2>Data Pelaku Usaha (Kepatuhan Perbup Kutim)</h2>
            <p>
                <label>Nomor Induk Kependudukan (NIK - 16 Digit): </label>
                <input v-model="form.nik" type="text" maxlength="16" placeholder="Contoh: 6408041234567890" required />
            </p>
            <p>
                <label>Nomor Kartu Keluarga (No KK - 16 Digit): </label>
                <input v-model="form.no_kk" type="text" maxlength="16" placeholder="Contoh: 6408049876543210" required />
            </p>
            <p>
                <label>Nama Pemilik (Sesuai KTP): </label>
                <input v-model="form.nama_pemilik" type="text" placeholder="Nama Lengkap" required />
            </p>
            <p>
                <label>Nama Usaha / Merk Dagang: </label>
                <input v-model="form.nama_usaha" type="text" placeholder="Contoh: Kripik Singkong Sangatta" required />
            </p>
            <p>
                <label>Nomor WhatsApp: </label>
                <input v-model="form.nomor_wa" type="text" placeholder="Contoh: 08123456789" required />
            </p>
            <p>
                <label>Kecamatan Domisili Usaha: </label>
                <select v-model="form.kecamatan" required>
                    <option value="Sangatta Utara">Sangatta Utara</option>
                    <option value="Sangatta Selatan">Sangatta Selatan</option>
                    <option value="Teluk Pandan">Teluk Pandan</option>
                    <option value="Rantau Pulung">Rantau Pulung</option>
                    <option value="Bengalun">Bengalon</option>
                    <option value="Kaliorang">Kaliorang</option>
                    <option value="Sangkulirang">Sangkulirang</option>
                    <option value="Muara Wahau">Muara Wahau</option>
                    <option value="Kombeng">Kongbeng</option>
                    <option value="Telen">Telen</option>
                    <option value="Muara Ancalong">Muara Ancalong</option>
                    <option value="Muara Bengkal">Muara Bengkal</option>
                    <option value="Busang">Busang</option>
                    <option value="Batu Ampar">Batu Ampar</option>
                    <option value="Long Mesangat">Long Mesangat</option>
                    <option value="Karangan">Karangan</option>
                    <option value="Kaubun">Kaubun</option>
                    <option value="Sandaran">Sandaran</option>
                </select>
            </p>
            <p>
                <label>Alamat Usaha: </label>
                <textarea v-model="form.alamat_usaha" rows="2" placeholder="Alamat lengkap tempat produksi/jualan"></textarea>
            </p>
            <p>
                <label>Nomor Induk Berusaha (NIB): </label>
                <input v-model="form.nib" type="text" placeholder="Opsional jika ada" />
            </p>
            <p>
                <label>
                    <input v-model="form.has_halal" type="checkbox" />
                    Memiliki Sertifikasi Halal
                </label>
            </p>
            <p>
                <label>
                    <input v-model="form.has_pirt" type="checkbox" />
                    Memiliki Izin P-IRT
                </label>
            </p>
            <p>
                <label>
                    <input v-model="form.pernah_terima_hibah" type="checkbox" />
                    Pernah Menerima Program Bantuan/Hibah Pemerintah
                </label>
            </p>
            <p v-if="form.pernah_terima_hibah">
                <label>Tahun Menerima Hibah: </label>
                <input v-model.number="form.tahun_terima_hibah" type="number" placeholder="Contoh: 2024" />
            </p>

            <hr />

            <h2>Katalog Menu Awal Stand</h2>
            <p>Masukkan menu produk yang dijual pada acara ini:</p>
            <div v-for="(item, index) in initialMenus" :key="index" style="margin-bottom: 8px;">
                <label>Menu #{{ index + 1 }}: </label>
                <input v-model="item.nama_item" type="text" placeholder="Nama Menu (cth: Es Teh)" />
                <label> Harga (Rp): </label>
                <input v-model.number="item.harga" type="number" min="0" placeholder="10000" />
                <button type="button" @click="removeMenuItem(index)" v-if="initialMenus.length > 1">Hapus</button>
            </div>
            <p>
                <button type="button" @click="addMenuItem">+ Tambah Item Menu</button>
            </p>

            <hr />

            <p>
                <button type="submit" :disabled="form.processing">Simpan & Kaitkan UMKM ke Stand</button>
            </p>
        </form>
    </div>
</template>
