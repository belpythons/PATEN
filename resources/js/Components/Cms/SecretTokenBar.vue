<script setup>
import { ref } from 'vue';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        default: '',
    },
    cmsUrl: {
        type: String,
        default: '',
    },
    publicUrl: {
        type: String,
        required: true,
    },
});

const isCopied = ref(false);
const isTokenCopied = ref(false);
const showFullToken = ref(false);

const copyCmsLink = () => {
    navigator.clipboard.writeText(props.cmsUrl).then(() => {
        isCopied.value = true;
        setTimeout(() => {
            isCopied.value = false;
        }, 2500);
    });
};

const copyFullToken = () => {
    if (!props.token) return;
    navigator.clipboard.writeText(props.token).then(() => {
        isTokenCopied.value = true;
        setTimeout(() => {
            isTokenCopied.value = false;
        }, 2500);
    });
};
</script>

<template>
    <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-5 shadow-xs mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Left: Security & Token summary -->
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[4px] bg-[#EDF2F9] text-[#132952] border border-[#CBD5E1] text-[11px] font-mono font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#00B262] animate-pulse"></span>
                        SESSION TERENKRIPSI
                    </span>
                    <span class="text-xs font-mono text-[#64748B]">KREDENSIAL MANAJEMEN EVENT</span>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-0.5">
                    <span class="text-xs font-mono text-[#64748B]">TOKEN:</span>
                    <code
                        class="px-2 py-1 bg-[#F8FAFC] border border-[#CBD5E1] rounded-[4px] text-xs font-mono text-[#132952] font-semibold tracking-wider cursor-pointer hover:border-[#132952] transition-colors"
                        :title="token"
                        @click="copyFullToken"
                    >
                        <template v-if="showFullToken">
                            {{ token || 'SESI LOGIN TERAUTENTIKASI' }}
                        </template>
                        <template v-else-if="token && token.length >= 24">
                            {{ token.substring(0, 16) }}••••••••••••••••••••••••••••••••{{ token.substring(token.length - 8) }}
                        </template>
                        <template v-else>
                            {{ token || 'SESI LOGIN TERAUTENTIKASI (PANITIA)' }}
                        </template>
                    </code>

                    <button
                        type="button"
                        class="text-[11px] font-mono text-[#5B6B8A] hover:text-[#132952] underline cursor-pointer"
                        @click="showFullToken = !showFullToken"
                    >
                        {{ showFullToken ? 'Sembunyikan' : 'Lihat Lengkap' }}
                    </button>

                    <button
                        type="button"
                        class="text-[11px] font-mono text-[#5B6B8A] hover:text-[#00B262] transition-colors cursor-pointer"
                        @click="copyFullToken"
                    >
                        <span v-if="isTokenCopied" class="text-[#00B262] font-bold">Token Tersalin! ✓</span>
                        <span v-else>Salin Token</span>
                    </button>
                </div>

                <p class="text-xs text-[#64748B] leading-relaxed pt-0.5">
                    Tautan CMS rahasia ini memberikan akses administratif penuh tanpa perlu password login. Simpan dan bagikan hanya kepada panitia terpercaya.
                </p>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <BaseButton
                    variant="primary"
                    size="sm"
                    class="cursor-pointer"
                    @click="copyCmsLink"
                >
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span v-if="isCopied">Link CMS Tersalin! ✓</span>
                    <span v-else>Salin Link CMS</span>
                </BaseButton>

                <a :href="publicUrl" target="_blank">
                    <BaseButton variant="outline" size="sm">
                        Buka Portal Publik &rarr;
                    </BaseButton>
                </a>
            </div>
        </div>
    </div>
</template>
