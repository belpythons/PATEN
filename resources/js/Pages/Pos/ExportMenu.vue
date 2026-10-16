<script setup>
const props = defineProps({
    stand: Object,
    event: Object,
    menus: Array,
});
</script>

<template>
    <div>
        <h1>Daftar Menu Stand: {{ stand.nomor_stand }}</h1>
        <p><strong>Nama Usaha:</strong> {{ stand.nama_usaha }} | <strong>Pengelola:</strong> {{ stand.nama_pemilik }}</p>
        <p><strong>Kategori:</strong> {{ stand.kategori_jualan }}</p>
        <p><strong>Event:</strong> {{ event.nama_event }}</p>
        <p>
            <strong>Warna Tema Event:</strong>
            Primer: <code>{{ event.color_primary }}</code> | Sekunder: <code>{{ event.color_secondary }}</code>
        </p>

        <hr />

        <h2>Katalog Menu & Harga</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Menu Produk</th>
                    <th>Harga Jual (Rp)</th>
                    <th>Status Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(m, idx) in menus" :key="m.id">
                    <td>{{ idx + 1 }}</td>
                    <td><strong>{{ m.nama_item }}</strong></td>
                    <td>Rp {{ Number(m.harga).toLocaleString('id-ID') }}</td>
                    <td>{{ m.is_available ? 'Tersedia' : 'Habis' }}</td>
                </tr>
            </tbody>
        </table>

        <hr />
        <p>
            <button type="button" onclick="window.print()">Cetak Lembar Menu (Print)</button>
        </p>
    </div>
</template>
