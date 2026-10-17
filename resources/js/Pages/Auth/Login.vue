<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/Navigation/BrandMark.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] text-[#132952] font-sans antialiased selection:bg-[#00B262] selection:text-white">
        <Head title="Masuk — Sistem PATEN Kutai Timur" />

        <!-- Minimal Header -->
        <header class="p-4 sm:p-6 bg-white border-b border-[#E2E8F0] flex items-center justify-between">
            <BrandMark :show-version="false" />
            <div class="flex items-center gap-3">
                <BaseBadge variant="emerald" :dot="true">
                    Akses Resmi
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
                        PORTAL PENGELOLA EVENT
                    </div>
                    <h1 class="text-2xl font-bold font-display text-[#132952] tracking-tight">
                        Masuk ke Dasbor PATEN
                    </h1>
                    <p class="text-xs text-[#5B6B8A]">
                        Khusus Panitia Pelaksana & Dinas Koperasi UKM Kutim
                    </p>
                </div>

                <!-- Session Status / Alert -->
                <div v-if="status" class="p-3 rounded-[6px] bg-[#00B262]/10 border border-[#00B262]/30 text-xs font-mono text-[#00B262]">
                    {{ status }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-medium text-[#132952]">
                            Alamat Email Resmi
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@kutaitimurkab.go.id"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.email" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-mono font-medium text-[#132952]">
                                Kata Sandi
                            </label>
                        </div>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-3.5 py-2.5 rounded-[6px] bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] text-[#132952] placeholder-[#94A3B8] text-sm focus:outline-none focus:border-[#132952] focus:ring-1 focus:ring-[#132952] transition-colors"
                        />
                        <p v-if="form.errors.password" class="text-xs font-mono text-[#EF4444]">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-mono text-[#5B6B8A]">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="rounded border-[#CBD5E1] bg-white text-[#00B262] focus:ring-0 focus:ring-offset-0"
                            />
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <BaseButton
                            type="submit"
                            variant="emerald"
                            class="w-full justify-center py-2.5 text-sm font-semibold tracking-wide"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Memverifikasi Kredensial...</span>
                            <span v-else>Masuk ke Dasbor &rarr;</span>
                        </BaseButton>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="pt-4 border-t border-[#F1F5F9] text-center space-y-2">
                    <p class="text-xs text-[#5B6B8A]">
                        Belum memiliki akun panitia?
                        <Link href="/register" class="text-[#00B262] hover:underline font-semibold ml-1">
                            Daftar Akun Panitia
                        </Link>
                    </p>
                    <p class="text-[11px] font-mono text-[#64748B]">
                        Pedagang stand bazar? Buka <Link href="/pos" class="text-[#FAA21B] hover:underline font-semibold">Gerbang Kasir POS</Link> dengan PIN
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
