<script setup>
import { Link, Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseCard from '@/Components/UI/BaseCard.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    status: {
        type: String,
        default: 'Sistem PATEN Siap',
    },
    version: {
        type: String,
        default: '1.0.0',
    },
    events: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <PublicLayout>
        <Head title="Beranda — Sistem Akuntabilitas Transaksi & Seleksi Bantuan UMKM" />

        <!-- Hero Section: Asymmetric Left-biased Triad Layout -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-10 sm:pt-16 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- Left Column (Copy & CTAs) -->
                <div class="lg:col-span-7">
                    <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#132952] font-semibold mb-3">
                        00 / EKOSISTEM AKUNTABILITAS NIAGA KUTIM
                    </p>
                    <h1 class="text-3xl sm:text-5xl font-display font-bold tracking-tight text-[#132952] leading-[1.15]">
                        Pemantauan Transaksi & Efektivitas Niaga.
                    </h1>
                    <p class="mt-4 text-base sm:text-lg text-[#273A60] leading-relaxed max-w-xl">
                        Infrastruktur digital satu pintu untuk pencatatan perputaran uang riil bazar daerah, pengelolaan kupon doorprize, dan kurasi otomatis bantuan UMKM berbasis Perbup Kutai Timur.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <Link href="/events/create">
                            <BaseButton variant="primary" size="lg">
                                Inisialisasi Event Baru &rarr;
                            </BaseButton>
                        </Link>
                        <Link href="/pos">
                            <BaseButton variant="emerald" size="lg">
                                Gerbang Kasir POS
                            </BaseButton>
                        </Link>
                        <Link href="/dashboard" v-if="$page.props.auth?.user">
                            <BaseButton variant="secondary" size="lg">
                                Dasbor Saya
                            </BaseButton>
                        </Link>
                    </div>

                    <!-- Subtle, professional inline navigation prompt for UMKM -->
                    <div class="mt-4 flex items-center gap-2 text-xs font-mono text-[#5B6B8A]">
                        <span>Pelaku Usaha UMKM?</span>
                        <a
                            href="#events-registry"
                            class="text-[#132952] font-semibold underline underline-offset-4 decoration-[#00B262] hover:text-[#00B262] transition-colors inline-flex items-center gap-1"
                        >
                            <span>Daftarkan profil stand bazar di sini</span>
                            <span>&darr;</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column (Live Graphite Instrument Card) -->
                <div class="lg:col-span-5">
                    <BaseCard variant="graphite" padding="normal" class="relative overflow-hidden">
                        <!-- Card window header -->
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#1E3A6E] text-xs font-mono text-[#94A3B8]">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#EF4444]/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#FAA21B]/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#00B262]/80"></span>
                                <span class="ml-2 text-[11px] text-[#CBD5E1]">paten-core.status</span>
                            </div>
                            <BaseBadge variant="emerald" :dot="true">
                                200 OK
                            </BaseBadge>
                        </div>

                        <!-- Technical stats readout -->
                        <div class="space-y-3 font-mono text-xs">
                            <div class="flex justify-between items-center py-1 border-b border-[#1E3A6E]/60">
                                <span class="text-[#94A3B8]">Target Populasi:</span>
                                <span class="text-white tnum font-semibold">20.000 UMKM Kutim</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#1E3A6E]/60">
                                <span class="text-[#94A3B8]">Target Hibah Modal:</span>
                                <span class="text-[#FAA21B] tnum font-semibold">Top 200 Kuota APBD</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#1E3A6E]/60">
                                <span class="text-[#94A3B8]">Kecepatan Kasir POS:</span>
                                <span class="text-[#00B262] tnum font-semibold">&le; 3 Detik / Tx</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-[#1E3A6E]/60">
                                <span class="text-[#94A3B8]">Guardrail Perbup:</span>
                                <span class="text-[#00B262] font-semibold">1 KK = 1 Usaha</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-[#94A3B8]">Metodologi SPK:</span>
                                <span class="text-white">SAW 5 Kriteria Berbobot</span>
                            </div>
                        </div>

                        <!-- Graphite mini terminal prompt -->
                        <div class="mt-4 pt-3 border-t border-[#1E3A6E] text-[11px] font-mono text-[#5B6B8A] flex items-center justify-between">
                            <span>Kutai Timur Niaga</span>
                            <span class="text-[#00B262]">SISTEM AKTIF</span>
                        </div>
                    </BaseCard>
                </div>
            </div>
        </section>

        <!-- Section: DIREKTORI EVENT AKTIF & PINTAS PENGUJIAN (Live Events Registry) -->
        <section id="events-registry" class="max-w-6xl mx-auto px-4 sm:px-6 py-12 border-t border-[#E2E8F0]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs font-mono uppercase tracking-[0.06em] text-[#132952] font-semibold">
                        01 / DIREKTORI EVENT AKTIF
                    </p>
                    <h2 class="text-2xl font-display font-bold text-[#132952] mt-1">
                        Daftar Event Bazar Kutai Timur di Sistem
                    </h2>
                    <p class="text-xs text-[#5B6B8A] mt-1 font-mono">
                        Klik pada event untuk menjelajahi portal publik, klasemen leaderboard, atau poster jadwal.
                    </p>
                </div>

                <div>
                    <Link href="/events/create">
                        <BaseButton variant="emerald" size="sm">
                            + Tambah Event Baru
                        </BaseButton>
                    </Link>
                </div>
            </div>

            <!-- UMKM Onboarding Informational Callout (Clean, No Emojis, Standard Cobalt Border) -->
            <div class="mb-6 px-4 py-3 rounded-[6px] bg-white border border-[#CBD5E1] border-l-4 border-l-[#00B262] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono uppercase font-semibold text-[11px] tracking-wider text-[#132952]">
                            Registrasi Peserta Stand Bazar UMKM
                        </span>
                        <BaseBadge variant="emerald" :dot="true">Terbuka</BaseBadge>
                    </div>
                    <p class="text-xs text-[#5B6B8A] mt-0.5">
                        Pilih event yang bertanda <span class="text-[#00B262] font-semibold">Slot Tersedia</span> untuk mengisi kelengkapan usaha (NIB, No. KK) dan daftar menu jualan.
                    </p>
                </div>
            </div>

            <!-- Event Cards Grid -->
            <div v-if="events && events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="ev in events"
                    :key="ev.id"
                    class="rounded-[10px] bg-white border border-[#CBD5E1] p-5 shadow-xs hover:border-[#132952] transition-all flex flex-col justify-between"
                >
                    <div>
                        <!-- Header info -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-[#64748B] px-2 py-0.5 rounded-[3px] bg-[#F1F5F9]">
                                {{ ev.penyelenggara }}
                            </span>
                            <BaseBadge v-if="ev.available_stands_count > 0" variant="emerald" :dot="true">
                                {{ ev.available_stands_count }} Stand Kosong
                            </BaseBadge>
                            <span v-else class="text-[11px] font-mono text-[#00B262] font-bold">
                                {{ ev.stands_count || 0 }} Stand
                            </span>
                        </div>

                        <h3 class="text-lg font-bold font-display text-[#132952]">
                            {{ ev.nama_event }}
                        </h3>

                        <p class="text-xs text-[#5B6B8A] mt-1 line-clamp-2">
                            {{ ev.lokasi_venue }} &middot; {{ new Date(ev.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }} - {{ new Date(ev.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </p>

                        <!-- Stand Quick Demonstration if available -->
                        <div v-if="ev.stands && ev.stands.length > 0" class="mt-4 pt-3 border-t border-[#F1F5F9]">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-[#94A3B8] block mb-1.5">
                                Uji Coba Stand Lapangan:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                <Link
                                    v-for="st in ev.stands"
                                    :key="st.id"
                                    :href="`/pos/${st.id}`"
                                    class="px-2 py-1 rounded-[4px] bg-[#F8FAFC] hover:bg-[#132952] hover:text-white text-[11px] font-mono border border-[#CBD5E1] transition-colors"
                                    :title="`Buka Terminal Kasir ${st.nomor_stand} (PIN: ${st.pin_pos})`"
                                >
                                    {{ st.nomor_stand }} &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Action Link Affordances -->
                    <div class="mt-5 pt-4 border-t border-[#E2E8F0] space-y-2">
                        <!-- Direct Daftar Stand Button if stands are available -->
                        <Link
                            v-if="ev.available_stands_count > 0"
                            :href="`/e/${ev.slug}#stands-section`"
                            class="inline-flex items-center justify-center w-full px-3 py-2 rounded-[6px] bg-[#00B262] hover:bg-[#009954] text-white text-xs font-mono font-semibold transition-colors shadow-xs"
                        >
                            Daftar Stand UMKM ({{ ev.available_stands_count }} Slot Tersedia) &rarr;
                        </Link>

                        <div class="grid grid-cols-2 gap-2">
                            <Link
                                :href="`/e/${ev.slug}`"
                                class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-[4px] bg-[#132952] hover:bg-[#0D1E3D] text-white text-xs font-mono font-medium transition-colors text-center"
                            >
                                Portal Publik ↗
                            </Link>
                            <Link
                                :href="`/e/${ev.slug}/leaderboard`"
                                class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-[4px] bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#132952] text-xs font-mono font-medium border border-[#CBD5E1] transition-colors text-center"
                            >
                                Leaderboard ↗
                            </Link>
                        </div>
                        <a
                            :href="`/events/${ev.slug}/poster-export`"
                            target="_blank"
                            class="inline-flex items-center justify-center w-full px-2 py-1.5 rounded-[4px] bg-white hover:bg-[#F8FAFC] text-[#5B6B8A] hover:text-[#132952] text-[11px] font-mono border border-[#E2E8F0] transition-colors text-center"
                        >
                            Poster Cetak
                        </a>
                    </div>
                </div>
            </div>

            <!-- Empty State when no events exist -->
            <div v-else class="p-8 rounded-[10px] bg-white border border-[#CBD5E1] text-center space-y-3">
                <p class="text-sm font-mono text-[#64748B]">
                    Belum ada event bazar yang terdaftar di database.
                </p>
                <Link href="/events/create">
                    <BaseButton variant="primary" size="sm">
                        Inisialisasi Event Pertama Sekarang &rarr;
                    </BaseButton>
                </Link>
            </div>
        </section>

    </PublicLayout>
</template>
