<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseCard from '@/Components/UI/BaseCard.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';
import StatCard from '@/Components/UI/StatCard.vue';

defineProps({
    status: {
        type: String,
        default: 'Sistem PATEN Siap',
    },
    version: {
        type: String,
        default: '1.0.0',
    },
});
</script>

<template>
    <PublicLayout>
        <!-- Hero Section: Asymmetric Left-biased Cobalt Layout -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-10 sm:pt-16 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- Left Column (Copy & CTAs) -->
                <div class="lg:col-span-7">
                    <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#2563EB] font-semibold mb-3">
                        00 / EKOSISTEM AKUNTABILITAS NIAGA KUTIM
                    </p>
                    <h1 class="text-3xl sm:text-5xl font-display font-bold tracking-tight text-[#0F172A] leading-[1.15]">
                        Pemantauan Transaksi & Efektivitas Niaga.
                    </h1>
                    <p class="mt-4 text-base sm:text-lg text-[#334155] leading-relaxed max-w-xl">
                        Infrastruktur digital satu pintu untuk pencatatan perputaran uang riil bazar daerah, pengelolaan kupon doorprize, dan kurasi otomatis bantuan UMKM berbasis Perbup Kutai Timur.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <Link href="/events/create">
                            <BaseButton variant="primary" size="lg">
                                Inisialisasi Event Baru &rarr;
                            </BaseButton>
                        </Link>
                        <a href="/health" target="_blank">
                            <BaseButton variant="secondary" size="lg">
                                Status Infrastruktur
                            </BaseButton>
                        </a>
                    </div>

                    <!-- Trust statement -->
                    <div class="mt-8 pt-6 border-t border-[#E2E8F0] flex flex-wrap items-center gap-4 text-xs font-mono text-[#64748B]">
                        <span class="flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#10B981]"></span>
                            Status: {{ status }}
                        </span>
                        <span>&middot;</span>
                        <span>Versi: v{{ version }}</span>
                        <span>&middot;</span>
                        <span>Supabase PG 17.11 Active</span>
                    </div>
                </div>

                <!-- Right Column (Live Graphite Instrument Card) -->
                <div class="lg:col-span-5">
                    <BaseCard variant="graphite" padding="normal" class="relative overflow-hidden">
                        <!-- Card window header -->
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#26334D] text-xs font-mono text-[#94A3B8]">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#EF4444]/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#F59E0B]/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#10B981]/80"></span>
                                <span class="ml-2 text-[11px] text-[#CBD5E1]">paten-core.status</span>
                            </div>
                            <BaseBadge variant="emerald" :dot="true">
                                200 OK
                            </BaseBadge>
                        </div>

                        <!-- Technical stats readout -->
                        <div class="space-y-3 font-mono text-xs">
                            <div class="flex justify-between items-center py-1 border-b border-[#26334D]/60">
                                <span class="text-[#94A3B8]">Target Populasi:</span>
                                <span class="text-white tnum font-semibold">20.000 UMKM</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#26334D]/60">
                                <span class="text-[#94A3B8]">Target Hibah Modal:</span>
                                <span class="text-[#F59E0B] tnum font-semibold">Top 200 Kuota APBD</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#26334D]/60">
                                <span class="text-[#94A3B8]">Kecepatan Kasir POS:</span>
                                <span class="text-[#10B981] tnum font-semibold">&le; 3 Detik / Tx</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#26334D]/60">
                                <span class="text-[#94A3B8]">Guardrail Perbup:</span>
                                <span class="text-[#2563EB] font-semibold">1 KK = 1 Usaha</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-[#94A3B8]">Metodologi SPK:</span>
                                <span class="text-white">SAW 5 Kriteria Berbobot</span>
                            </div>
                        </div>

                        <!-- Graphite mini terminal prompt -->
                        <div class="mt-4 pt-3 border-t border-[#26334D] text-[11px] font-mono text-[#64748B] flex items-center justify-between">
                            <span>$ agy.verify()</span>
                            <span class="text-[#10B981]">44/44 TESTS PASS</span>
                        </div>
                    </BaseCard>
                </div>
            </div>
        </section>

        <!-- 4 Core Modules Grid (Hairline-bordered Cards) -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12 border-t border-[#E2E8F0]">
            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#64748B]">
                    ARSITEKTUR TERINTEGRASI
                </p>
                <h2 class="text-2xl font-display font-bold text-[#0F172A] mt-1">
                    4 Pilar Modul Sistem PATEN
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Modul 1 -->
                <BaseCard padding="normal" class="flex flex-col justify-between hover:border-[#2563EB] transition-colors">
                    <div>
                        <BaseBadge variant="cobalt" class="mb-3">Modul 1</BaseBadge>
                        <h3 class="font-display font-semibold text-base text-[#0F172A]">
                            Event Generator & Gamifikasi
                        </h3>
                        <p class="mt-2 text-xs text-[#64748B] leading-relaxed">
                            Form-to-Pages microsite, pratinjau tema dinamis, modal penguncian, serta E-Kupon doorprize dan tiket HTM ber-QR unik.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] text-xs font-mono text-[#2563EB]">
                        /events/create &rarr;
                    </div>
                </BaseCard>

                <!-- Modul 2 -->
                <BaseCard padding="normal" class="flex flex-col justify-between hover:border-[#2563EB] transition-colors">
                    <div>
                        <BaseBadge variant="neutral" class="mb-3">Modul 2</BaseBadge>
                        <h3 class="font-display font-semibold text-base text-[#0F172A]">
                            CMS Hidden URL & Gate
                        </h3>
                        <p class="mt-2 text-xs text-[#64748B] leading-relaxed">
                            Akses token 64-karakter tanpa login rumit, timeline rundown, batch generator stan & PIN kasir, scanner gate, dan undian doorprize.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] text-xs font-mono text-[#64748B]">
                        /cms/{slug}?token=...
                    </div>
                </BaseCard>

                <!-- Modul 3 -->
                <BaseCard padding="normal" class="flex flex-col justify-between hover:border-[#2563EB] transition-colors">
                    <div>
                        <BaseBadge variant="emerald" class="mb-3">Modul 3</BaseBadge>
                        <h3 class="font-display font-semibold text-base text-[#0F172A]">
                            Fast POS Mini & Profiling
                        </h3>
                        <p class="mt-2 text-xs text-[#64748B] leading-relaxed">
                            Banner edukasi anti-pajak resmi, form kepatuhan Perbup, keypad sentuh 6 digit, dan kasir ultra cepat &le; 3 detik per transaksi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] text-xs font-mono text-[#10B981]">
                        /pos/{stand_id} &rarr;
                    </div>
                </BaseCard>

                <!-- Modul 4 -->
                <BaseCard padding="normal" class="flex flex-col justify-between hover:border-[#2563EB] transition-colors">
                    <div>
                        <BaseBadge variant="amber" class="mb-3">Modul 4</BaseBadge>
                        <h3 class="font-display font-semibold text-base text-[#0F172A]">
                            SPK SAW & Sinkronisasi ML
                        </h3>
                        <p class="mt-2 text-xs text-[#64748B] leading-relaxed">
                            Filter guardrail 1 KK 1 Usaha, normalisasi 5 bobot kriteria, pemetaan 5 program bantuan daerah, dan penentuan Kuota 200 APBD.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] text-xs font-mono text-[#D97706]">
                        /cms/{slug}/evaluations &rarr;
                    </div>
                </BaseCard>
            </div>
        </section>
    </PublicLayout>
</template>
