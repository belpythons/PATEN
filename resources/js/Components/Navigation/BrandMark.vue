<script setup>
import { computed, useAttrs } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: '/',
    },
    showVersion: {
        type: Boolean,
        default: false,
    },
    variant: {
        type: String,
        default: 'auto',
    },
    imgClass: {
        type: String,
        default: 'h-8 w-auto',
    },
});

const attrs = useAttrs();

const isDark = computed(() => {
    if (props.variant === 'white' || props.variant === 'dark') {
        return true;
    }
    if (props.variant === 'light') {
        return false;
    }
    const cls = String(attrs.class || '');
    return cls.includes('text-white') || cls.includes('dark');
});

const logoSrc = computed(() => {
    return isDark.value
        ? '/images/logo-horizontal-white.png'
        : '/images/logo-horizontal.png';
});
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-2 group select-none">
        <img
            :src="logoSrc"
            alt="PATEN — Pemantauan Transaksi & Efektivitas Niaga"
            :class="[imgClass, 'object-contain transition-opacity duration-150 group-hover:opacity-90']"
            loading="eager"
        />
        <span
            v-if="showVersion"
            class="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 rounded-[4px] border"
            :class="isDark ? 'bg-white/10 text-white/90 border-white/20' : 'bg-[#EDF2F9] text-[#132952] border-[#C2D2E9]'"
        >
            kutim
        </span>
    </Link>
</template>

