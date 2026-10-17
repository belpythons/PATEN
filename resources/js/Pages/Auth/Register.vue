<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const form = useForm({
    name: '',
    email: '',
    role: 'organizer',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] text-[#132952] font-sans antialiased selection:bg-[#00B262] selection:text-white">
        <Head title="Pendaftaran Akun Panitia — Sistem PATEN Kutai Timur" />

        <!-- Minimal Header -->
        <header class="p-4 sm:p-6 bg-white border-b border-[#E2E8F0] flex items-center justify-between">
            <BrandMark :show-version="false" />
            <div class="flex items-center gap-3">
                <BaseBadge variant="emerald" :dot="true">
                    Registrasi Panitia
                </BaseBadge>
                <Link
                    href="/"
                    class="text-xs font-mono text-[#5B6B8A] hover:text-[#132952] transition-colors"
                >
                    &larr; Beranda
                </Link>
            </div>
        </header>

        <!-- Main Card Section -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-8">
            <div class="w-full max-w-md bg-white border border-[#CBD5E1] rounded-[10px] p-6 sm:p-8 shadow-xs space-y-6">
                <!-- Title Header -->
                <div class="space-y-1.5 text-center">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#00B262]/10 border border-[#00B262]/25 text-[11px] font-mono text-[#00B262] font-semibold mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00B262] animate-pulse"></span>
                        REGISTRASI PENGELOLA
                    </div>
                    <h1 class="text-2xl font-bold font-display text-[#132952] tracking-tight">
                        Buat Akun Panitia PATEN
                    </h1>
                    <p class="text-xs text-[#5B6B8A]">
                        Kelola event bazar, stand UMKM, doorprize & kurasi bantuan modal
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Name Input -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-mono font-medium text-[#132952]">
                            Nama Lengkap / Instansi
                        </label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            placeholder="Contoh: Panitia Pekan Raya Sangatta"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.name" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-medium text-[#132952]">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            placeholder="panitia@kutim.go.id"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.email" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-1.5">
                        <label for="role" class="block text-xs font-mono font-medium text-[#132952]">
                            Peran Akun
                        </label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        >
                            <option value="organizer">Panitia / Penyelenggara Event (EO / Paguyuban)</option>
                            <option value="admin_dinas">Dinas Koperasi & UKM Kutim (Akses Supervisi & Evaluator)</option>
                        </select>
                        <p v-if="form.errors.role" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.role }}
                        </p>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-mono font-medium text-[#132952]">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.password" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-mono font-medium text-[#132952]">
                            Konfirmasi Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            placeholder="Ulangi kata sandi"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.password_confirmation" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <BaseButton
                            type="submit"
                            variant="emerald"
                            class="w-full justify-center py-2.5 text-sm font-semibold tracking-wide"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mendaftarkan Akun...</span>
                            <span v-else>Daftar & Masuk ke Dasbor &rarr;</span>
                        </BaseButton>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="pt-4 border-t border-[#F1F5F9] text-center space-y-2">
                    <p class="text-xs text-[#5B6B8A]">
                        Sudah memiliki akun?
                        <Link href="/login" class="text-[#00B262] hover:underline font-semibold ml-1">
                            Masuk Sekarang
                        </Link>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="p-4 bg-white border-t border-[#E2E8F0] text-center text-xs font-mono text-[#5B6B8A]">
            Pemerintah Kabupaten Kutai Timur &middot; Dinas Koperasi, Usaha Kecil dan Menengah
        </footer>
    </div>
</template>
