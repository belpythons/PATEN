<script setup>
const props = defineProps({
    event: Object,
    awarding_tier: String,
    leaderboard: Array,
});
</script>

<template>
    <div>
        <h1>Klasemen Keaktifan Stand UMKM - {{ event.nama_event }}</h1>
        <p><strong>Penyelenggara:</strong> {{ event.penyelenggara }}</p>
        <p><strong>Lokasi Acara:</strong> {{ event.lokasi_venue }}</p>
        <p><small>Penghargaan keaktifan dinilai berdasarkan konsistensi pelayanan dan transaksi digital selama gelaran acara.</small></p>

        <hr />

        <h2>Daftar Peringkat Stand Teraktif (Awarding)</h2>
        <table border="1" cellpadding="8" cellspacing="0" v-if="leaderboard && leaderboard.length">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Nomor Stand</th>
                    <th>Nama Usaha UMKM</th>
                    <th>Poin Keaktifan</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in leaderboard" :key="item.peringkat">
                    <td>
                        <strong>#{{ item.peringkat }}</strong>
                    </td>
                    <td>{{ item.nomor_stand }}</td>
                    <td><strong>{{ item.nama_usaha }}</strong></td>
                    <td>{{ item.poin_keaktifan }} Poin</td>
                </tr>
            </tbody>
        </table>
        <p v-else>
            <em>Belum ada catatan aktivitas transaksi yang terekam pada leaderboard.</em>
        </p>

        <hr />
        <p>
            <a :href="`/e/${event.slug}`">&laquo; Kembali ke Halaman Utama Acara</a>
        </p>
    </div>
</template>
