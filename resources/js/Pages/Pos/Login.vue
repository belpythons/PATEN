<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    stand: Object,
    event: Object,
    umkm: Object,
    errors: Object,
});

const form = useForm({
    pin_pos: '',
});

const submit = () => {
    form.post(`/pos/${props.stand.id}/login-pin`);
};
</script>

<template>
    <div>
        <h1>Login Kasir POS Mini - {{ stand.nomor_stand }}</h1>
        <p><strong>Event:</strong> {{ event.nama_event }}</p>
        <p><strong>Pelaku Usaha:</strong> {{ umkm ? umkm.nama_usaha + ' (' + umkm.nama_pemilik + ')' : 'Belum Terdaftar' }}</p>
        <p><strong>Kategori Jualan:</strong> {{ stand.kategori_jualan }}</p>

        <hr />

        <div v-if="form.errors && form.errors.pin_pos">
            <p><strong>[LOGIN GAGAL]:</strong> {{ form.errors.pin_pos }}</p>
        </div>

        <form @submit.prevent="submit">
            <p>
                <label>Masukkan Kode PIN Kasir Stand (6 Digit Angka): </label>
                <br /><br />
                <input
                    v-model="form.pin_pos"
                    type="password"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="******"
                    autofocus
                    required
                />
            </p>
            <p>
                <button type="submit" :disabled="form.processing">Buka Terminal Kasir</button>
            </p>
        </form>

        <hr />
        <p><small>Jika Anda belum memiliki PIN kasir, silakan hubungi Panitia Pengelola Acara.</small></p>
    </div>
</template>
