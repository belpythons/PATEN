<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    stand: Object,
    event: Object,
    umkm: Object,
    menus: Array,
    recent_transactions: Array,
});

// Flash messages
const statusMessage = ref('');
const errorMessage = ref('');

// Fast POS Transaction Input Form
const txForm = useForm({
    menu_id: props.menus && props.menus.length ? props.menus[0].id : '',
    qty: 1,
    metode_pembayaran: 'cash',
});

const selectedMenu = computed(() => {
    return (props.menus || []).find(m => m.id === txForm.menu_id) || null;
});

const subtotalPreview = computed(() => {
    if (!selectedMenu.value) return 0;
    return Number(selectedMenu.value.harga) * Number(txForm.qty || 1);
});

const submitTransaction = () => {
    statusMessage.value = '';
    errorMessage.value = '';
    if (!txForm.menu_id) {
        errorMessage.value = 'Silakan pilih menu terlebih dahulu.';
        return;
    }

    txForm.post(`/pos/${props.stand.id}/transactions`, {
        preserveScroll: true,
        onSuccess: () => {
            statusMessage.value = `Transaksi ${selectedMenu.value?.nama_item} (x${txForm.qty}) Rp ${subtotalPreview.value.toLocaleString('id-ID')} berhasil dicatat!`;
            txForm.qty = 1;
        },
        onError: (errs) => {
            errorMessage.value = 'Gagal mencatat transaksi: ' + Object.values(errs).join(', ');
        },
    });
};

// Quick Menu Addition Form
const newMenuForm = useForm({
    nama_item: '',
    harga: '',
});

const submitNewMenu = () => {
    newMenuForm.post(`/pos/${props.stand.id}/menus`, {
        preserveScroll: true,
        onSuccess: () => {
            statusMessage.value = 'Menu baru berhasil ditambahkan!';
            newMenuForm.reset();
        },
    });
};
</script>

<template>
    <div>
        <h1>Terminal Kasir POS Mini - {{ stand.nomor_stand }}</h1>
        <p><strong>Event:</strong> {{ event.nama_event }}</p>
        <p><strong>Stand / UMKM:</strong> {{ umkm ? umkm.nama_usaha : 'Stand Umum' }} ({{ umkm ? umkm.nama_pemilik : '-' }})</p>
        <p>
            <a :href="`/pos/${stand.id}/export-menu`" target="_blank">[Buka Lembar Desain Menu Stand]</a>
        </p>

        <hr />

        <div v-if="statusMessage">
            <p><strong>[STATUS SUKSES]:</strong> {{ statusMessage }}</p>
        </div>
        <div v-if="errorMessage">
            <p><strong>[STATUS ERROR]:</strong> {{ errorMessage }}</p>
        </div>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Total Transaksi Terekam</th>
                    <th>Total Omzet Terekam</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ stand.total_transaksi_terekam || 0 }} Transaksi</strong></td>
                    <td><strong>Rp {{ Number(stand.total_omzet_terekam || 0).toLocaleString('id-ID') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <hr />

        <h2>1. Catat Transaksi Cepat Kasir (&le; 3 Detik)</h2>
        <div v-if="menus && menus.length">
            <form @submit.prevent="submitTransaction">
                <p>
                    <label>Pilih Menu: </label>
                    <select v-model="txForm.menu_id" required>
                        <option v-for="m in menus" :key="m.id" :value="m.id">
                            {{ m.nama_item }} - Rp {{ Number(m.harga).toLocaleString('id-ID') }}
                        </option>
                    </select>
                </p>
                <p>
                    <label>Jumlah (Qty): </label>
                    <input v-model.number="txForm.qty" type="number" min="1" max="100" required />
                </p>
                <p>
                    <label>Metode Pembayaran: </label>
                    <select v-model="txForm.metode_pembayaran" required>
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS / Digital</option>
                    </select>
                </p>
                <p>
                    <strong>Estimasi Subtotal: Rp {{ subtotalPreview.toLocaleString('id-ID') }}</strong>
                </p>
                <p>
                    <button type="submit" :disabled="txForm.processing">Simpan Transaksi (Enter / Tap)</button>
                </p>
            </form>
        </div>
        <div v-else>
            <p><em>Belum ada menu produk aktif pada stand ini. Silakan tambahkan menu di bawah ini terlebih dahulu.</em></p>
        </div>

        <hr />

        <h2>2. Riwayat 10 Transaksi Terakhir</h2>
        <table border="1" cellpadding="4" cellspacing="0" v-if="recent_transactions && recent_transactions.length">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Menu Produk</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="tx in recent_transactions" :key="tx.id">
                    <td>{{ new Date(tx.created_at).toLocaleTimeString('id-ID') }}</td>
                    <td>{{ tx.menu ? tx.menu.nama_item : '-' }}</td>
                    <td>{{ tx.qty }}</td>
                    <td>Rp {{ Number(tx.subtotal).toLocaleString('id-ID') }}</td>
                    <td>{{ tx.metode_pembayaran.toUpperCase() }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else>Belum ada riwayat transaksi.</p>

        <hr />

        <h2>3. Tambah Item Menu Baru</h2>
        <form @submit.prevent="submitNewMenu">
            <p>
                <label>Nama Menu / Produk: </label>
                <input v-model="newMenuForm.nama_item" type="text" placeholder="Contoh: Kopi Aren Sangatta" required />
            </p>
            <p>
                <label>Harga Jual (Rp): </label>
                <input v-model.number="newMenuForm.harga" type="number" min="0" placeholder="15000" required />
            </p>
            <p>
                <button type="submit" :disabled="newMenuForm.processing">+ Tambah ke Katalog Stand</button>
            </p>
        </form>
    </div>
</template>
