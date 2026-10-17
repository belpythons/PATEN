<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stand: {
        type: Object,
        required: true,
    },
    event: {
        type: Object,
        required: true,
    },
    menus: {
        type: Array,
        default: () => [],
    },
});

const handlePrint = () => {
    if (typeof window !== 'undefined') {
        window.print();
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#F1F5F9] print:bg-white text-[#132952] font-sans antialiased py-6 print:py-0 px-4 print:px-0">
        <Head :title="`Lembar Menu Cetak — ${stand.nomor_stand} (${stand.nama_usaha})`" />

        <!-- Print Action Floating Bar (Hidden during print) -->
        <div class="max-w-3xl mx-auto mb-6 print:hidden flex items-center justify-between bg-white p-4 rounded-[8px] border border-[#CBD5E1] shadow-xs">
            <div class="flex items-center gap-3">
                <a
                    :href="`/pos/${stand.id}`"
                    class="inline-flex items-center gap-1.5 text-xs font-mono text-[#5B6B8A] hover:text-[#132952] transition-colors"
                >
                    ← Kembali ke Terminal Kasir
                </a>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-[6px] bg-[#132952] text-white text-xs font-mono font-semibold hover:bg-[#0C1830] transition-colors cursor-pointer shadow-sm"
                    @click="handlePrint"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Lembar Meja (A4)</span>
                </button>
            </div>
        </div>

        <!-- A4 Printable Sheet Container -->
        <main
            class="max-w-3xl mx-auto bg-white border border-[#CBD5E1] print:border-none shadow-md print:shadow-none p-8 sm:p-12 rounded-[2px]"
            :style="{ '--event-primary': event.color_primary, '--event-secondary': event.color_secondary }"
        >
            <!-- Top Hairline Header with Event Theme Color Band -->
            <div
                class="h-2 w-full rounded-xs mb-6"
                :style="{ backgroundColor: event.color_primary || '#132952' }"
            ></div>

            <!-- Header Info -->
            <div class="border-b-2 pb-6 flex items-start justify-between gap-6" :style="{ borderColor: event.color_primary || '#132952' }">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 rounded-[3px] text-white text-xs font-mono font-bold tracking-wider uppercase"
                            :style="{ backgroundColor: event.color_primary || '#132952' }"
                        >
                            {{ stand.nomor_stand }}
                        </span>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#64748B]">
                            {{ stand.kategori_jualan }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold font-display text-[#132952] mt-2">
                        {{ stand.nama_usaha }}
                    </h1>
                    <p class="text-xs font-mono text-[#64748B] mt-1">
                        Pengelola: <strong class="text-[#132952]">{{ stand.nama_pemilik }}</strong> &middot; Event: <strong class="text-[#132952]">{{ event.nama_event }}</strong>
                    </p>
                </div>

                <!-- Official Seal Emblem -->
                <div class="text-right shrink-0">
                    <p class="text-[10px] font-mono uppercase font-bold text-[#64748B] tracking-wider">
                        Pemerintah Kab. Kutai Timur
                    </p>
                    <p class="text-xs font-semibold text-[#132952]">
                        Dinas Koperasi & UKM
                    </p>
                    <p class="text-[10px] font-mono text-[#94A3B8]">
                        PATEN Official Partner
                    </p>
                </div>
            </div>

            <!-- Menu Table Section -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-mono uppercase tracking-wider font-bold text-[#132952]">
                        Daftar Menu & Harga Resmi
                    </h2>
                    <span class="text-[11px] font-mono text-[#64748B]">
                        Semua harga sudah termasuk pajak/retribusi
                    </span>
                </div>

                <div class="border border-[#CBD5E1] rounded-[4px] overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#CBD5E1] font-mono text-[11px] text-[#64748B] uppercase">
                                <th class="py-2.5 px-4 w-12 text-center">No</th>
                                <th class="py-2.5 px-4">Nama Menu Produk</th>
                                <th class="py-2.5 px-4 text-right w-36">Harga (Rp)</th>
                                <th class="py-2.5 px-4 text-center w-28">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] text-xs">
                            <tr
                                v-for="(item, idx) in menus"
                                :key="item.id"
                                class="hover:bg-[#F8FAFC]"
                            >
                                <td class="py-3 px-4 font-mono text-center text-[#64748B]">
                                    {{ idx + 1 }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-[#132952]">
                                    {{ item.nama_item }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-[#132952] text-right">
                                    Rp {{ Number(item.harga).toLocaleString('id-ID') }}
                                </td>
                                <td class="py-3 px-4 font-mono text-center">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-[3px] text-[10px] font-bold uppercase"
                                        :class="item.is_available ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#FEE2E2] text-[#B91C1C]'"
                                    >
                                        {{ item.is_available ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!menus || menus.length === 0">
                                <td colspan="4" class="py-6 text-center text-xs font-mono text-[#94A3B8]">
                                    Belum ada menu produk aktif yang didaftarkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Section: QRIS Payment Box + Government Compliance Notice -->
            <div class="mt-8 pt-6 border-t border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <!-- QRIS Barcode Box Frame -->
                <div class="p-4 rounded-[6px] border-2 border-dashed border-[#CBD5E1] text-center flex flex-col items-center justify-center min-h-[140px] bg-[#F8FAFC]">
                    <div class="h-16 w-16 border border-[#132952] rounded-[4px] flex items-center justify-center p-1 bg-white mb-2">
                        <!-- Stylized QR Code Graphic -->
                        <svg class="h-12 w-12 text-[#132952]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h2v2h-2v-2zm-4-2h2v2h-2v-2zm2 4h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm0-4h2v2h-2v-2zm-4-4h2v2h-2v-2z" />
                        </svg>
                    </div>
                    <p class="text-xs font-mono font-bold text-[#132952]">
                        TERIMA PEMBAYARAN QRIS
                    </p>
                    <p class="text-[10px] font-mono text-[#64748B]">
                        Scan QRIS stand atau minta kasir untuk transaksi tunai
                    </p>
                </div>

                <!-- Anti-Tax Official Legal Seal -->
                <div class="space-y-2 text-left">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[3px] bg-[#FFFBEB] border border-[#FAA21B]/40 text-[#B45309] text-[10px] font-mono font-bold">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#FAA21B]"></span>
                        <span>GARANSI PORTOFOLIO BANTUAN DISKOP-UKM</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-[#5B6B8A]">
                        Pencatatan transaksi kasir pada stand ini merupakan data portofolio seleksi penerima Program Bantuan Modal dan Alat Usaha Pemerintah Kabupaten Kutai Timur.
                    </p>
                    <p class="text-[10px] font-mono text-[#94A3B8]">
                        Sistem PATEN (Platform Akuntabilitas Transaksi & Efektivitas Niaga) &middot; &copy; {{ new Date().getFullYear() }} Pemkab Kutim
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 1cm;
    }
    body {
        background-color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
