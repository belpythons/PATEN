<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'navy',
        validator: (value) => ['navy', 'cobalt', 'emerald', 'ruby', 'amber', 'neutral', 'graphite'].includes(value),
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'emerald':
            return 'bg-[#E6F7EF] text-[#008046] border-[#A2E8C6]';
        case 'ruby':
            return 'bg-[#FEF2F2] text-[#DC2626] border-[#FECACA]';
        case 'amber':
            return 'bg-[#FEF6E8] text-[#C87D0E] border-[#FCDDA3]';
        case 'neutral':
            return 'bg-[#F1F5F9] text-[#273A60] border-[#CBD5E1]';
        case 'graphite':
            return 'bg-[#0C1830] text-[#E2E8F0] border-[#1E3A6E]';
        case 'navy':
        case 'cobalt':
        default:
            return 'bg-[#EDF2F9] text-[#132952] border-[#C2D2E9]';
    }
});

const dotClasses = computed(() => {
    switch (props.variant) {
        case 'emerald': return 'bg-[#00B262]';
        case 'ruby': return 'bg-[#EF4444]';
        case 'amber': return 'bg-[#FAA21B]';
        case 'neutral': return 'bg-[#5B6B8A]';
        case 'graphite': return 'bg-[#5B6B8A]';
        case 'navy':
        case 'cobalt':
        default: return 'bg-[#132952]';
    }
});
</script>

<template>
    <span :class="['inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[6px] border text-xs font-mono uppercase tracking-[0.06em] font-medium leading-none select-none', variantClasses]">
        <span v-if="dot" :class="['h-1.5 w-1.5 rounded-full shrink-0', dotClasses]" aria-hidden="true"></span>
        <slot />
    </span>
</template>
