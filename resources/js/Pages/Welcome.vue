<script setup>
defineProps({
    status: {
        type: String,
        default: 'Sistem PATEN Siap'
    },
    version: {
        type: String,
        default: '1.0.0'
    }
});
</script>

<template>
    <div>
        <h1>PATEN - Pemantauan Transaksi & Efektivitas Niaga</h1>
        <p>Status: {{ status }}</p>
        <p>Versi: {{ version }}</p>
    </div>
</template>
