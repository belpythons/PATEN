<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'cobalt',
        validator: (value) => ['cobalt', 'emerald', 'ruby', 'amber', 'neutral', 'graphite'].includes(value),
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'emerald':
            return 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]';
        case 'ruby':
            return 'bg-[#FEF2F2] text-[#DC2626] border-[#FECACA]';
        case 'amber':
            return 'bg-[#FFFBEB] text-[#D97706] border-[#FDE68A]';
        case 'neutral':
            return 'bg-[#F1F5F9] text-[#334155] border-[#CBD5E1]';
        case 'graphite':
            return 'bg-[#1E293B] text-[#E2E8F0] border-[#334155]';
        default:
            return 'bg-[#EFF6FF] text-[#2563EB] border-[#BFDBFE]';
    }
});

const dotClasses = computed(() => {
    switch (props.variant) {
        case 'emerald': return 'bg-[#10B981]';
        case 'ruby': return 'bg-[#EF4444]';
        case 'amber': return 'bg-[#F59E0B]';
        case 'neutral': return 'bg-[#64748B]';
        case 'graphite': return 'bg-[#94A3B8]';
        default: return 'bg-[#2563EB]';
    }
});
</script>

<template>
    <span :class="['inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[6px] border text-xs font-mono uppercase tracking-[0.06em] font-medium leading-none select-none', variantClasses]">
        <span v-if="dot" :class="['h-1.5 w-1.5 rounded-full shrink-0', dotClasses]" aria-hidden="true"></span>
        <slot />
    </span>
</template>
