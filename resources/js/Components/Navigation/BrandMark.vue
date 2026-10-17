<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        default: '/',
    },
    showVersion: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-1.5 group select-none">
        <span class="font-display font-bold text-lg tracking-tight text-[#0F172A] flex items-center">
            <span class="text-[#2563EB] font-mono mr-0.5 text-xl font-semibold">/</span>
            <span>paten</span>
        </span>
        <span v-if="showVersion" class="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 rounded-[4px] bg-[#F1F5F9] text-[#64748B] border border-[#CBD5E1]">
            kutim
        </span>
    </Link>
</template>
